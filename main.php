<?php 
	date_default_timezone_set('Asia/Manila');
	header('Access-Control-Allow-Origin: *');
	header('Content-Type: application/json');

	include_once './config/Database.php';
	// include_once './models/Post.php';
	include_once './models/Auth.php';

	$database = new Database();
	$db = $database->connect();

	// $post = new Post($db);
	$auth = new Auth($db);

	$data = array();
	$req = explode('/', rtrim($_REQUEST['request'], '/'));

	switch ($_SERVER['REQUEST_METHOD']) {
		case 'POST':
			switch($req[0]){
				
				case 'login':
					$d = json_decode(base64_decode(file_get_contents("php://input")));
					echo json_encode($auth->login_user($d));
				break;
				
				case 'register':
					$d = json_decode(base64_decode(file_get_contents("php://input")));
					echo json_encode($auth->register_user($d));
				break;

				case 'review':
					$d = json_decode(base64_decode(file_get_contents("php://input")));
					echo json_encode($auth->review_cateringservice($d));
				break;

				case 'update':
					$d = json_decode(base64_decode(file_get_contents("php://input")));
					echo json_encode($auth->update_profile($d));
				break;

				case 'loadCurrentUser':
					$d = json_decode(base64_decode(file_get_contents("php://input")));
					echo json_encode($auth->loadCurrentUser($d));
				break;

				case 'checkUsernameEmailExist':
					$d = json_decode(base64_decode(file_get_contents("php://input")));
					echo json_encode($auth->checkUsernameEmailExist($d));
				break;

				case 'addCompany':
					$d = json_decode(base64_decode(file_get_contents("php://input")));
					echo json_encode($auth->addCompany($d));
				break;

				case 'deleteCompany':
					$d = json_decode(base64_decode(file_get_contents("php://input")));
					echo json_encode($auth->deleteCompany($d));
				break;

				case 'getReviews':
                    echo json_encode($auth->generalQuery("SELECT * FROM tbl_reviews"));
				break;

				case 'updateCompany':
					$d = json_decode(base64_decode(file_get_contents("php://input")));
					echo json_encode($auth->updateCompany($d));				
				break;

				case 'getUsers':
                    echo json_encode($auth->generalQuery("SELECT * FROM tbl_user"));
				break;

				case 'getCompanies':
                    echo json_encode($auth->generalQuery("SELECT * FROM tbl_companies"));
				break;

				case 'deleteUser':
					$d = json_decode(base64_decode(file_get_contents("php://input")));
                    echo json_encode($auth->deleteUser($d));
				break;

				case 'getReviewsOnLanors':
                    echo json_encode($auth->generalQuery("SELECT * FROM tbl_reviews WHERE review_service = 'La Nors Kitchen'"));
				break;
				
				case 'getReviewsOnRicos':
                    echo json_encode($auth->generalQuery("SELECT * FROM tbl_reviews WHERE review_service = 'Ricos Fastfood & Restaurant'"));
				break;
				
				case 'getReviewsOnEdwins':
                    echo json_encode($auth->generalQuery("SELECT * FROM tbl_reviews WHERE review_service = 'Edwins Restaurant'"));
				break;
				
				case 'getReviewsOnLosPolluelos':
                    echo json_encode($auth->generalQuery("SELECT * FROM tbl_reviews WHERE review_service = 'Los Polluelos Lechon & Restaurant'"));
                break;

				default:
					http_response_code(400);
					echo "Bad Request";
				break;
			}
		
		break;

		case 'GET':
			switch ($req[0]) {
				
				case 'user':
					if(count($req)>1){
						echo json_encode($auth->select('tbl_'.$req[0], $req[1]),JSON_PRETTY_PRINT);
					} else {
						echo json_encode($auth->select('tbl_'.$req[0], null),JSON_PRETTY_PRINT);
					}
					
				break;

				case 'reviews':
					if(count($req)>1){
						echo json_encode($auth->select('tbl_'.$req[0], $req[1]),JSON_PRETTY_PRINT);
					} else {
						echo json_encode($auth->select('tbl_'.$req[0], null),JSON_PRETTY_PRINT);
					}
					
				break;

				default:
					http_response_code(400);
					echo "Bad Request";
				break;
			}
	}

?>