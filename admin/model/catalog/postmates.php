<?php
class ModelCatalogPostmates extends Model {
	
	//added zipcdoe
	public function addPostmates($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "Postmates SET 
			Postmates = '" . (int)$data['Postmates'] . "',
			city = '" . $this->db->escape($data['city'])  . "', 
			store_id = '" . (int)$data['store_id'] . "',
			date_added = NOW()");

			$Postmates_id = $this->db->getLastId();

		return $Postmates_id;
	}

	//edit Postmates
	public function editPostmates($Postmates_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "Postmates SET 
		Postmates = '" . (int)$data['Postmates'] . "',
		city = '" . $this->db->escape($data['city'])  . "', 
		store_id = '" . (int)$data['store_id'] . "',
		status = '" . (int)$data['status'] . "',
		date_added = NOW() WHERE id = '" . (int)$Postmates_id . "'");
		
	}

	//delete Postmates
	public function deletePostmates($Postmates_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "Postmates WHERE id = '" . (int)$Postmates_id . "'");
	}

	//get single Postmates
	public function getPostmates($Postmates_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "Postmates z  
		LEFT JOIN " . DB_PREFIX . "store s ON (z.store_id = s.store_id)
		WHERE z.id = '" . (int)$Postmates_id . "'");

		return $query->row;
	}

	//get Postmatess	
	public function getPostmatess($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "Postmates z 
		LEFT JOIN " . DB_PREFIX . "store s ON (z.store_id = s.store_id)";

		$sql .= "WHERE z.date_added	is not null ";

		if (!empty($data['filter_city'])) {
			$sql .= " AND z.city LIKE '" . $this->db->escape($data['filter_city']) . "%'";
		}

		if (!empty($data['filter_Postmates'])) {
			$sql .= " AND z.Postmates LIKE '" . $this->db->escape($data['filter_Postmates']) . "%'";
		}

		if (!empty($data['filter_name'])) {
			$sql .= " AND s.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND z.status = '" . (int)$data['filter_status'] . "'";
		}

		$sql .= " GROUP BY z.id";

		$sort_data = array(
			'z.city',
			'z.Postmates',
			's.name',
			'z.status',
			'z.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY z.city";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	//filters	
	public function getPostmatesFilters($Postmates_id) {
		$Postmates_filter_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "Postmates_filter WHERE Postmates_id = '" . (int)$Postmates_id . "'");

		foreach ($query->rows as $result) {
			$Postmates_filter_data[] = $result['filter_id'];
		}

		return $Postmates_filter_data;
	}

	//get total Postmatess use for paging
	public function getTotalPostmatess($data = array()) {
		$sql = "SELECT COUNT(DISTINCT z.id) AS total FROM " . DB_PREFIX . "Postmates z 
		LEFT JOIN " . DB_PREFIX . "store s ON (z.store_id = s.store_id)";

		$sql .= "WHERE z.date_added	is not null ";

		if (!empty($data['filter_city'])) {
			$sql .= " AND z.city LIKE '" . $this->db->escape($data['filter_city']) . "%'";
		}

		if (!empty($data['filter_Postmates'])) {
			$sql .= " AND z.Postmates LIKE '" . $this->db->escape($data['filter_Postmates']) . "%'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND z.status = '" . (int)$data['filter_status'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}


}
