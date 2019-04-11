<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;
// Use the REST API Client to make requests to the Twilio REST API
use Twilio\Rest\Client;

class Sms extends REST_Controller {

	public function __construct()
	{
		header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
		header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
		header("Access-Control-Allow-Origins: *");
		parent::__construct();
        $this->load->database();
	}

	public function index(){}

	public function enviarsms_get(){
        $data = ['phone' => '+523323515151', 'text' => 'Se ha registrado un nuevo reporte de mantenimiento'];
        $sid = 'ACa569d590ff5c25f921081974b1814fbe';
         $token = '9930f5ae927125ce99de0f48ca548358';
         $client = new Client($sid, $token);
    // Use the client to do fun stuff like send text messages!
    return $client->messages->create(
        // the number you'd like to send the message to
        $data['phone'],
        array(
            // A Twilio phone number you purchased at twilio.com/console
            'from' => '+12053468451',
            // the body of the text message you'd like to send
            'body' => $data['text']
        )
        );
        //print_r($this->enviar_sms($data));
    }
    public function registrarnumero_post(){
        //SI NO SE ENVIA TOKEN NI EL ID DEL USUARIO
		$token = $this->post('token');
		$idUsuario = $this->post('idUsuario');
		if($token === "" || $idUsuario === ""){
			$respuesta = array('error' => TRUE,
								'mensaje' => 'No Autorizado');
			$this->response($respuesta,REST_Controller::HTTP_UNAUTHORIZED);
			return;
		}
		//VALIDAR SI EL TOKEN ENVIADO CORRESPONDE AL ID DEL USUARIO QUE SOLICITA
		$condiciones = array('id' => $idUsuario,
							 'token' => $token );
		$this->db->where($condiciones);
		$query = $this->db->get('personal');
		$existe = $query->row();
		if (!$existe) {
			$respuesta = array('error' => TRUE,
								'mensaje' => 'Usuario y token incorrectos');
			$this->response($respuesta,REST_Controller::HTTP_UNAUTHORIZED);
			return;
		}
        //AQUI YA ESTA VALIDADO EL USUARIO
		$this->db->reset_query();        
		//SE PREPARAN LOS DATOS A INSERTAR
        $condiciones = array('telefono' => $this->post('telefono'));
			$this->db->where('id',$idUsuario);
			$resultado = $this->db->update('personal',$condiciones);
		//SE ENVIA LA RESPUESTA
		$respuesta = array('error' => FALSE,
							'mensaje' => 'Se ha registrado el número de telefono correctamente.');

		$this->response($respuesta);
    }
    
}