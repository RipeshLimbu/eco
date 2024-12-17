<?php
class QueryBuilder {
    private $db;
    
    public function __construct(Database $db) {
        $this->db = $db;
    }
    
    public function getAssignmentDetails($assignmentId, $collectorId) {
        $sql = "
            SELECT 
                a.*, 
                c.title,
                c.description,
                c.location,
                u.full_name as user_name,
                u.phone as user_phone
            FROM assignments a
            JOIN complaints c ON a.complaint_id = c.id
            JOIN users u ON c.user_id = u.id
            WHERE a.id = ? AND a.collector_id = ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $assignmentId, $collectorId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getCollectorStats($collectorId) {
        $sql = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
            FROM assignments
            WHERE collector_id = ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $collectorId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
} 