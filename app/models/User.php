<?php

class User {
	private $db;

	public function __construct()
	{
		$this->db = new Database();
	}

	// Register user
	public function register($data)
	{ // Create Query
		$this->db->query("INSERT INTO users (name, email, password) VALUES(:name, :email, :password)");
		// Bind values
		$this->db->bind(':name', $data['name']);
		$this->db->bind(':email', $data['email']);
		$this->db->bind(':password', $data['password']);
		if ($this->db->execute()) {
			return true;
		} else {
			return false;
		}
	}

	public function findUserByEmail($email)
	{
		$this->db->query("SELECT * FROM users WHERE email = :email");
		// Bind value
		$this->db->bind(':email', $email);

		$this->db->single();

		// Check row
		if ($this->db->rowCount() > 0) {
			return true;
		} else {
			return false;
		}
	}
}