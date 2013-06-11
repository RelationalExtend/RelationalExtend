<?php

/**
 * Permissions and roles management
 *
 * @author     Ahmed Maawy
 * @copyright  2013 Ahmed Maawy
 */
 
 class Permissions
 {
 	const TABLE_ROLES = "roles";
	const TABLE_PERMISSIONS = "permissions";
	const TABLE_ROLES_PERMISSIONS = "roles_permissions";
	const TABLE_USER_ROLES = "user_roles";
	
	/**
	 * Get all roles
	 * 
	 * @return roles 
	 */
	
	public static function get_roles()
	{
		$rows = DB::select("*")->from(self::TABLE_ROLES)
			->order_by("role")
			->as_object()->execute();
			
		return $rows;
	}
	
	/**
	 * Get all permissions
	 * 
	 * @return permissions 
	 */
	
	public static function get_permissions()
	{
		$rows = DB::select("*")->from(self::TABLE_PERMISSIONS)
			->order_by("permission")
			->as_object()->execute();
			
		return $rows;
	}
	
	/**
	 * Get all roles for a user
	 * 
	 * @param $user_id
	 * @return roles 
	 */
	
	public static function get_user_roles($user_id)
	{
		$rows = DB::select("*")->from(self::TABLE_USER_ROLES)
			->where("user_id", "=", $user_id)
			->as_object()->execute();
			
		return $rows;
	}
	
	/**
	 * Get all permissions for a role
	 * 
	 * @param $role_id
	 * @return permissions 
	 */
	
	public static function get_roles_permissions($role_id)
	{
		$rows = DB::select("*")->from(self::TABLE_ROLES_PERMISSIONS)
			->where("role_id", "=", $role_id)
			->as_object()->execute();
			
		return $rows;
	}
	
	/**
	 * Creates a role
	 * 
	 * @param $role
	 * @return role_id 
	 */
	
	public static function create_role($role)
	{
		$role_slug = Utility::slugify($role);
		
		// Check to see if exists
		$slug_count = DB::select(DB::expr('count(*) AS num_rows'))
			->from(self::TABLE_ROLES)
			->where("role_slug", "=", $role_slug)
			->as_object()->execute();
		
		// Exists?	
		if($slug_count[0]->num_rows > 0)
			return 0;
		
		// Insert if not
		list($insert_id, $rows) = DB::insert(self::TABLE_ROLES)
			->set(
				array
				(
					"role" => $role,
					"role_slug" => $role_slug
				)
			)->execute();
		
		return $insert_id;
	}
	
	/**
	 * Update a role
	 * 
	 * @param $role_id
	 * @param $role_name
	 * @return result 
	 */
	
	public static function update_role($role_id, $role_name)
	{
		$result = DB::update(self::TABLE_ROLES)
			->set(array(
				"role" => $role_name,
				"role_slug" => Utility::slugify($role_name)
			))
			->where("id", "=", $role_id)
			->execute();
			
		return $result;
	}
	
	/**
	 * Create a permission
	 * 
	 * @param $permission
	 * @return permission_id
	 */
	
	public static function create_permission($permission)
	{
		$permission_slug = Utility::slugify($permission);
		
		// Check to see if exists
		$slug_count = DB::select(DB::expr('count(*) AS num_rows'))
			->from(self::TABLE_PERMISSIONS)
			->where("permission_slug", "=", $permission_slug)
			->as_object()->execute();
		
		// Exists?	
		if($slug_count[0]->num_rows > 0)
			return 0;
		
		// Insert if not
		list($insert_id, $rows) = DB::insert(self::TABLE_PERMISSIONS)
			->set(
				array
				(
					"permission" => $permission,
					"permission_slug" => $permission_slug
				)
			)->execute();
		
		return $insert_id;
	}
	
	/**
	 * Update permissions
	 * 
	 * @param $permission_id
	 * @param $permission_name
	 * @return result
	 */
	
	public static function update_permission($permission_id, $permission_name)
	{
		$result = DB::update(self::TABLE_PERMISSIONS)
			->set(array(
				"permission" => $permission_name,
				"permission_slug" => Utility::slugify($permission_name)
			))
			->where("id", "=", $permission_id)
			->execute();
			
		return $result;
	}
	
	/**
	 * Builds roles for a user
	 * 
	 * @param $user_id 
	 */
	
	private static function build_roles_for_user($user_id)
	{
		$roles = self::get_user_roles($user_id);
		$roles_array = array();
		
		foreach($roles as $role)
			$roles_array[] = $role->role_id;
		
		$roles_not_in = array();
		
		if(count($roles_array) > 0)
			$roles_not_in = DB::select("*")->from(self::TABLE_ROLES)
				->where("id", "not in", $roles_array)
				->as_object()->execute();
		else
			$roles_not_in = DB::select("*")->from(self::TABLE_ROLES)
				->as_object()->execute();
			
		foreach($roles_not_in as $role_not_in)
		{
			DB::insert(self::TABLE_USER_ROLES)
				->set(array(
					"user_id" => $user_id,
					"role_id" => $role_not_in->id,
					"active" => 0
				))->execute();
		}
	}
	
	/**
	 * Build permissions for a role
	 * 
	 * @param $role_id 
	 */
	
	private static function build_permissions_for_role($role_id)
	{
		$permissions = self::get_roles_permissions($role_id);
		$permissions_array = array();
		
		foreach($permissions as $permission)
			$permissions_array[] = $permission->permission_id;
		
		$permissions_not_in = array();
		
		if(count($permissions_array) > 0)
			$permissions_not_in = DB::select("*")->from(self::TABLE_PERMISSIONS)
				->where("id", "not in", $permissions_array)
				->as_object()->execute();
		else
			$permissions_not_in = DB::select("*")->from(self::TABLE_PERMISSIONS)
				->as_object()->execute();
			
		foreach($permissions_not_in as $permission_not_in)
		{
			DB::insert(self::TABLE_ROLES_PERMISSIONS)
				->set(array(
					"role_id" => $role_id,
					"permission_id" => $permission_not_in->id,
					"active" => 0
				))->execute();
		}
	}
	
	/**
	 * Assigns a role to a user
	 * 
	 * @param $user_id
	 * @param $role_id
	 * @return result
	 */
	
	public static function assign_user_role($user_id, $role_id)
	{
		$result = DB::update(self::TABLE_USER_ROLES)
			->set(array(
				"active" => 1
			))
			->where("user_id", "=", $user_id)
			->and_where("role_id", "=", $role_id)
			->execute();
			
		return $result;
	}
	
	/**
	 * Deassigns a role to a user
	 * 
	 * @param $user_id
	 * @param $role_id
	 * @return result
	 */
	
	public static function deassign_user_role($user_id, $role_id)
	{
		$result = DB::update(self::TABLE_USER_ROLES)
			->set(array(
				"active" => 0
			))
			->where("user_id", "=", $user_id)
			->and_where("role_id", "=", $role_id)
			->execute();
			
		return $result;
	}
	
	/**
	 * Assigns roles to a user
	 * 
	 * @param $user_id
	 * @param $role_ids
	 */
	
	public static function assign_user_roles($user_id, $role_ids = array())
	{
		self::build_roles_for_user($user_id);
				
		foreach($role_ids as $role_id)
		{
			self::assign_user_role($user_id, $role_id);
		}
	}
	
	/**
	 * Assigns a permission to a role
	 * 
	 * @param $role_id
	 * @param $permission_id
	 * @return result
	 */
	
	public static function assign_role_permission($role_id, $permission_id)
	{
		$result = DB::update(self::TABLE_ROLES_PERMISSIONS)
			->set(array(
				"active" => 1
			))
			->where("role_id", "=", $role_id)
			->and_where("permission_id", "=", $permission_id)
			->execute();
			
		return $result;
	}
	
	/**
	 * Deassigns a permission to a role
	 * 
	 * @param $role_id
	 * @param $permission_id
	 * @return result
	 */
	
	public static function deassign_role_permission($role_id, $permission_id)
	{
		$result = DB::update(self::TABLE_ROLES_PERMISSIONS)
			->set(array(
				"active" => 0
			))
			->where("role_id", "=", $role_id)
			->and_where("permission_id", "=", $permission_id)
			->execute();
			
		return $result;
	}
	
	/**
	 * Assigns permissions to a role
	 * 
	 * @param $role_id
	 * @param $permission_ids
	 */
	
	public static function assign_role_permissions($role_id, $permission_ids = array())
	{
		self::build_permissions_for_role($role_id);
		
		foreach($permission_ids as $permission_id)
		{
			self::assign_role_permission($role_id, $permission_id);
		}
	}
	
	/**
	 * Delete a role
	 * 
	 * @param $role_id
	 * @return result
	 */
	
	public static function delete_role($role_id)
	{
		DB::start_transaction();
		
		// Delete all role related stuff
		$result = DB::delete(self::TABLE_ROLES_PERMISSIONS)
			->where("role_id", "=", $role_id)
			->execute();
		
		$result = DB::delete(self::TABLE_USER_ROLES)
			->where("role_id", "=", $role_id)
			->execute();
		
		$result = DB::delete(self::TABLE_ROLES)
			->where("id", "=", $role_id)
			->execute();
		
		DB::commit_transaction();
		
		return true;
	}
	
	/**
	 * Delete a permission
	 * 
	 * @param $permission_id
	 * @return result
	 */
	
	public static function delete_permission($permission_id)
	{
		DB::start_transaction();
		
		// Delete all permission related stuff
		$result = DB::delete(self::TABLE_ROLES_PERMISSIONS)
			->where("permission_id", "=", $permission_id)
			->execute();
		
		$result = DB::delete(self::TABLE_PERMISSIONS)
			->where("id", "=", $permission_id)
			->execute();
		
		DB::commit_transaction();
		
		return true;
	}
	
	/**
	 * Asseses if a user is permitted to do something specific
	 * 
	 * @param $user_id
	 * @param $permission_slug
	 * @return boolean
	 */
	
	public static function is_user_permitted($user_id, $permission_slug)
	{
		$permission = DB::select("*")->from(self::TABLE_PERMISSIONS)
			->where("permission_slug", "=", $permission_slug)
			->as_object()->execute();
		
		if(count($permission) < 1)
			throw new Exception_CMS("The permission $permission_slug does not exist");
		
		$permission_id = $permission[0]->id;
		
		$role = DB::select("*")->from(self::TABLE_USER_ROLES)
			->join(self::TABLE_ROLES_PERMISSIONS, 'LEFT')
			->on(self::TABLE_USER_ROLES.".role_id", "=", self::TABLE_ROLES_PERMISSIONS.".role_id")
			->where(self::TABLE_USER_ROLES.".user_id", "=", $user_id)
			->and_where(self::TABLE_ROLES_PERMISSIONS.".permission_id", "=", $permission_id)
			->and_where(self::TABLE_USER_ROLES.".active", "=", 1)
			->and_where(self::TABLE_ROLES_PERMISSIONS.".active", "=", 1)
			->as_object()->execute();
			
		return (count($role) > 0);
	}
 }
