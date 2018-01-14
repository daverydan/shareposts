<?php 

class Users extends Controller {
	public function __construct()
	{
		$this->userModel = $this->model('User');
	}

	public function register()
	{
		// Check if POST
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			// process form
	
			// Sanitize POST data
			$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

			$data = [
				'name' => trim($_POST['name']),
				'email' => trim($_POST['email']),
				'password' => trim($_POST['password']),
				'confirm_password' => trim($_POST['confirm_password']),
				'name_err' => '',
				'email_err' => '',
				'password_err' => '',
				'confirm_password_err' => '',
			];

			// Validations
			if (empty($data['email'])) {
				$data['email_err'] = 'Please enter email';
			} else {
				if ($this->userModel->findUserByEmail($data['email'])) {
					$data['email_err'] = 'Email is already taken';
				}
			}
			if (empty($data['name'])) {
				$data['name_err'] = 'Please enter name';
			}
			if (empty($data['password'])) {
				$data['password_err'] = 'Please enter password';
			} elseif(strlen($data['password']) < 6) {
				$data['password_err'] = 'Password must be at least 6 characters';
			}
			if (empty($data['confirm_password'])) {
				$data['confirm_password_err'] = 'Please confirm password';
			} else {
				if ($data['password'] != $data['confirm_password']) {
					$data['confirm_password_err'] = 'Passwords do not match';
				}
			}

			// Check no errors
			if (empty($data['email_err']) && empty($data['name_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {
				// Validate

				// Hash password
				$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

				// Register user
				if ($this->userModel->register($data)) {
					flash('register_success', 'Registered successfully');
					redirect('users/login');
				} else {
					die('Something went wrong!');
				}
			} else {
				// Load view with errors
				$this->view('users/register', $data);
			}

		} else {
			// load form
			$data = [
				'name' => '',
				'email' => '',
				'password' => '',
				'confirm_password' => '',
				'name_err' => '',
				'email_err' => '',
				'password_err' => '',
				'confirm_password_err' => '',
			];

			$this->view('users/register', $data);
		}
	}

	public function login()
	{
		// Check if POST
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			// process form

			// Sanitize POST data
			$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

			$data = [
				'email' => trim($_POST['email']),
				'password' => trim($_POST['password']),
				'email_err' => '',
				'password_err' => '',
			];

			// Validations
			if (empty($data['email'])) {
				$data['email_err'] = 'Please enter your email';
			}
			if (empty($data['password'])) {
				$data['password_err'] = 'Please enter your password';
			}

			// Check for user email
			if ($this->userModel->findUserByEmail($data['email'])) {
				// User found
			} else {
				$data['email_err'] = 'No user found';
			}

			// Check no errors
			if (empty($data['email_err']) && empty($data['password_err'])) {
				// Check & set logged in user
				$loggedInUser = $this->userModel->login($data['email'], $data['password']);

				if ($loggedInUser) {
					// Create session
					$this->createSessionUser($loggedInUser);
				} else {
					$data['password_err'] = 'Password incorrect';
					$this->view('users/login', $data);
				}
			} else {
				// Load view with errors
				$this->view('users/login', $data);
			}
			
		} else {
			// load form
			// echo 'load form';
			$data = [
				'email' => '',
				'password' => '',
				'email_err' => '',
				'password_err' => '',
			];

			$this->view('users/login', $data);
		}
	}

	public function createSessionUser($user)
	{
		$_SESSION['user_id'] = $user->id;
		$_SESSION['user_email'] = $user->email;
		$_SESSION['user_name'] = $user->name;
		redirect('pages/index');
	}

	public function logout()
	{
		unset($_SESSION['user_id']);
		unset($_SESSION['user_email']);
		unset($_SESSION['user_name']);
		session_destroy();
		redirect('users/login');
	}

	public function isLoggedIn()
	{
		if (isset($_SESSION['user_id'])) {
			return true;
		} else {
			return false;
		}
	}
}