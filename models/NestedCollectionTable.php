<?php
class NestedCollectionTable extends Omeka_Db_Table
{
    /**
     * Fetch all collections that can be assigned as a parent collection to the 
     * specified collection.
     * 
     * @param null|int $collectionId
     * @return array An array of collection rows.
     */
    public function fetchAssignableParentCollections($collectionId)
    {
        $db = $this->getDb();
        
        // Must cast null collection ID to 0 to properly bind.
        $collectionId = (int) $collectionId;
        
        $sql = "
        SELECT * 
        FROM {$db->Collection} 
        WHERE id != ?";
        
        return $db->fetchAll($sql, array($collectionId));
    }
    
    /**
     * Find parent/child relationship by child collection ID.
     * 
     * @param int $childCollectionId
     * @return Omeka_Record
     */
    public function findByChildCollectionId($childCollectionId)
    {
        $db = $this->getDb();
        
        $sql = "
        SELECT * 
        FROM {$db->NestedCollection} 
        WHERE child_collection_id = ?";
        
        // Child collection IDs are unique, so only fetch one row.
        return $this->fetchObject($sql, array($childCollectionId));
    }
    
    /**
     * Fetch the children of the specified collection.
     * 
     * @param int $collectionId
     * @return array
     */
    public function fetchChildren($collectionId)
    {
        $db = $this->getDb();
        
        $sql = "
        SELECT c.* 
        FROM {$db->Collection} c 
        JOIN {$db->NestedCollection} nc 
        ON c.id = nc.child_collection_id 
        WHERE nc.parent_collection_id = ?";
        
        return $this->fetchAll($sql, $collectionId);
    }
    
    /**
     * Fetch the parent of the specified collection.
     * 
     * @param int $collectionId
     * @return array
     */
    public function fetchParent($collectionId)
    {
        $db = $this->getDb();
        
        $sql = "
        SELECT c.* 
        FROM {$db->Collection} c 
        JOIN {$db->NestedCollection} nc 
        ON c.id = nc.parent_collection_id 
        WHERE nc.child_collection_id = ?";
        
        return $this->fetchRow($sql, $collectionId);
    }
    
    public function fetchCollections()
    {
        $db = $this->getDb();
        $sql = "
        SELECT c.*, nc.parent_collection_id 
        FROM {$db->Collection} c 
        LEFT JOIN {$db->NestedCollection} nc 
        ON c.id = nc.child_collection_id";
        return $db->fetchAll($sql);
    }
}
