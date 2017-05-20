<?php
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

class ControllerBase extends Controller {
	/**
	 * Main init function
	 */
	public function initialize() {
		// code ...
		$this->flash->output ();
	}
	public function fechaExcel($xl_date) {
		$PHPTimeStamp = PHPExcel_Shared_Date::ExcelToPHP ( $xl_date );
		$fechaExcel = date ( 'Y-m-d', $PHPTimeStamp );
		// $fechaExcel = date_format((($xl_date - 25569) * 86400), "Y-m-d H:i:s");
		return $fechaExcel;
	}
	public function fechaMySQLx($xl_date) {
		$PHPTimeStamp = PHPExcel_Shared_Date::ExcelToPHP ( $xl_date );
		$fechaExcel = date ( 'Y-m-d', $PHPTimeStamp );
		return $fechaExcel;
	}
	public function fechaHoraMySQLx($xl_date) {
		$PHPTimeStamp = PHPExcel_Shared_Date::ExcelToPHP ( $xl_date );
		$fechaExcel = date ( 'Y-m-d H:i:s', $PHPTimeStamp );
		return $fechaExcel;
	}
	public function fechaHoy($conHora) {
		$timezone = - 6;
		if ($conHora == true) {
			return gmdate ( "Y-m-d H:i:s", time () + 3600 * ($timezone) );
		} else {
			return gmdate ( "Y-m-d", time () + 3600 * ($timezone) );
		}
	}
        
        /**
         * Fecha para ingrear a mysql
         */
        public function fechaMySQL($hora, $today = true, $fecha = "") {
            if($today){
                $hoy = $this->fechaHoy(false);
                $mysqltime = date ("Y-m-d H:i:s", "$hoy $hora");
                return $mysqltime;
            }
        }
        
        /**
         * Comparar tiempo
         */
        public function comparaTiempo($tiempo) {
            date_default_timezone_set('America/El_Salvador');
            if ((time() + (30 * 60)) >= strtotime($tiempo)) {
              return true;
            }else{
                return false;
            }
        }
	
	// Sends the json response
	public function sendJson($data) {
		$this->view->disable ();
		$this->response->setContentType ( 'application/json', 'UTF-8' );
		$this->response->setContent ( json_encode ( $data));
		return $this->response;
	}
        /*
	public function elemento($t, $n, $l, $r = 0) {
		$dId = "";
		if (! is_numeric ( $r )) {
			$dId = "id='$r'";
		}
		$elem = "";
		switch ($t) {
			case "i" :
				if(count($n)> 2){
					$elem = $elem."<b>$l</b><br>";
					$elem = $elem . "<img id='$n[0]' src='$l' onclick ='$n[1]' height='$n[2]' width='$n[3]'>";
					$elem = $elem . $this->tag->hiddenField ( array (
						n."$n[0]",
						"value" => 0 
				) );
				}else{
					$elem = $elem . "<img id='$n[0]' src='$l' onclick ='$n[1]'>";
				}				
				break;
			case "hr" :
				$elem = $elem . "<hr>";
				break;
			case "h" :
				$elem = $elem . $this->tag->hiddenField ( array (
						"$n[0]",
						"value" => $l 
				) );
				break;
			case "s" :
				$elem = $elem . '<div class="form-group main"><div class="col-sm-12" align="center" ' . $dId . '>';
				$elem = $elem . $this->tag->submitButton ( array (
						"$l",
						"class" => "btn btn-default" 
				) );
				$elem = $elem . '</div></div>';
				break;
			case "bg" :
				$elem = $elem . '<div class="form-group edit"><div class="col-sm-12" align="center" ' . $dId . '>';
				foreach ( $n as $b ) {
					$elem = $elem . '<button class="btn btn-default" id="' . $b [0] . '" name="' . $b [0] . '" onclick="' . $b [1] . '">' . $b [2] . '</button> ';
				}
				$elem = $elem . '</div></div>';
				break;
			case "h2" :
				$elem = $elem . '<h2>' . $l . '</h2>';
				break;
			case "h1" :
				$elem = $elem . '<div class="page-header" ' . $dId . '><h1>' . $l . '</h1></div>';
				break;
			case "l" :
				$elem = $elem . '<div class="form-group"><label for="' . $l . '" class="col-sm-2 control-label">' . $l . '</label>';
				$elem = $elem . '<div class="col-sm-2 control-label" ' . $dId . '>' . $n [0] . '</div></div>';
				break;
			case "lf" :
				$elem = $elem . '<div class="form-group" ' . $dId . '><label for="' . $n [0] . '" class="col-sm-12">' . $l . '</label></div>';
				break;
			case "enter" :
				$elem = $elem . '<nobr>&nbsp;</nobr>';
				break;			
			default :
				$elem = '<div class="form-group"><label for="';
				// agregamos el nombre
				$elem = $elem . $n [0] . '" class="col-sm-2 control-label">';
				// agrega label
				$elem = $elem . $l . '</label><div class="col-sm-10" ' . $dId . '>';
				// agrega nombre campo
				switch ($t) {
					case "t" :
						if ($r == 1) {
							$elem = $elem . $this->tag->textField ( array (
									"$n[0]",
									"size" => 30,
									"class" => "form-control",
									"id" => "$n[0]",
									"readonly" => "" 
							) );
						} else {
							$elem = $elem . $this->tag->textField ( array (
									"$n[0]",
									"size" => 30,
									"class" => "form-control",
									"id" => "$n[0]" 
							) );
						}
						break;
					case "tv" :
						if ($r == 1) {
							$elem = $elem . $this->tag->textField ( array (
									"$n[0]",
									"size" => 30,
									"class" => "form-control",
									"id" => "$n[0]",
									"value" => "$n[1]",
									"readonly" => "" 
							) );
						} else {
							$elem = $elem . $this->tag->textField ( array (
									"$n[0]",
									"size" => 30,
									"class" => "form-control",
									"id" => "$n[0]",
									"value" => "$n[1]" 
							) );
						}
						break;
					case "m" :
						$elem = $elem . $this->tag->textField ( array (
								"$n[0]",
								"size" => 30,
								"class" => "form-control number",
								"id" => "$n[0]",
								"value" => "$n[1]" 
						) );
						break;
					case "e" :
						$elem = $elem . $this->tag->textField ( array (
								"$n[0]",
								"size" => 30,
								"class" => "form-control email",
								"id" => "$n[0]" 
						) );
						break;
					case "p" :
						$elem = $elem . $this->tag->passwordField ( array (
								"$n[0]",
								"size" => 30,
								"class" => "form-control",
								"id" => "$n[0]" 
						) );
						break;
					case "d" :
						$elem = $elem . $this->tag->dateField ( array (
								"$n[0]",
								"min" => "0",
								"size" => 30,
								"class" => "form-control date datepicker",
								"id" => "$n[0]" 
						) );
						break;
					case "sdb" :
						if (count ( $n ) > 3) {
							$elem = $elem . $this->tag->select ( array (
									"$n[0]",
									$n [1],
									"using" => $n [2],
									"class" => "form-control",
									"id" => "$n[0]",
									"value" => $n [3] 
							) );
						} else {
							$elem = $elem . $this->tag->select ( array (
									"$n[0]",
									$n [1],
									"using" => $n [2],
									"class" => "form-control",
									"id" => "$n[0]" 
							) );
						}
						break;
					case "sel" :
						if (count ( $n ) > 2) {
							$elem = $elem . $this->tag->select ( array (
									"$n[0]",
									$n [1],
									"class" => "form-control",
									"id" => "$n[0]",
									"value" => $n [2] 
							) );
						} else {
							$elem = $elem . $this->tag->select ( array (
									"$n[0]",
									$n [1],
									"class" => "form-control",
									"id" => "$n[0]" 
							) );
						}
						break;
					case "r" :
						foreach ( $n [1] as $rb ) {
							$elem = $elem . "<label for='$rb'>$rb</label>";
							$elem = $elem . $this->tag->radioField ( array (
									"$n[0]",
									"value" => "$rb",
									"id" => "$rb" 
							) );
							$elem = $elem . "&nbsp;";
						}
						break;
					case "ls" :
						$elem = $elem . $this->tag->textField ( array (
								"$n[0]",
								"size" => 30,
								"class" => "form-control",
								"id" => "$n[0]",
								"onkeyup" => "$n[1]"							
						) );
						$elem = $elem."</div><div id=\"livesearch\">";
						break;
					case "f" :
						$elem = $elem.$this->tag->fileField("$n[0]");
						break;
				}
				
				$elem = $elem . '</div></div>';
		}
		return $elem;
	}
	
         * 
         */
        
        public function elemento($t, $n, $l, $r = 0, $v = "") {
		$dId = "";
		if (! is_numeric ( $r )) {
			$dId = "id='$r'";
		}
		$elem = "";
		switch ($t) {
			case "i" :
				$elem = $elem . "<img id='$n[0]' src='$l' onclick ='$n[1]'>";
				break;
			case "hr" :
				$elem = $elem . "<hr>";
				break;
			case "h" :
				$elem = $elem . $this->tag->hiddenField ( array (
						"$n[0]",
						"value" => $l 
				) );
				break;
			case "s" :
				$elem = $elem . '<div class="form-group main"><div class="col-sm-12" align="center" ' . $dId . '>';
				$elem = $elem . $this->tag->submitButton ( array (
						"$l",
						"class" => "btn btn-default" 
				) );
				$elem = $elem . '</div></div>';
				break;
			case "bg" :
				$elem = $elem . '<div class="form-group edit"><div class="col-sm-12" align="center" ' . $dId . '>';
				foreach ( $n as $b ) {
					$elem = $elem . '<button class="btn btn-default" id="' . $b [0] . '" name="' . $b [0] . '" onclick="' . $b [1] . '">' . $b [2] . '</button> ';
				}
				$elem = $elem . '</div></div>';
				break;
			case "h2" :
				$elem = $elem . '<h2>' . $l . '</h2>';
				break;
			case "h1" :
				$elem = $elem . '<div class="page-header" ' . $dId . '><h1>' . $l . '</h1></div>';
				break;
			case "l" :
				$elem = $elem . '<div class="form-group"><label for="' . $l . '" class="col-sm-2 control-label">' . $l . '</label>';
				$elem = $elem . '<div class="col-sm-10 control-label" ' . $dId . '>' . $n [0] . '</div></div>';
				break;
			case "lf" :
				$elem = $elem . '<div class="form-group" ' . $dId . '><label for="' . $n [0] . '" class="col-sm-12">' . $l . '</label></div>';
				break;
			case "enter" :
				$elem = $elem . '<nobr>&nbsp;</nobr>';
				break;			
			default :
				$elem = '<div class="form-group"><label for="';
				// agregamos el nombre
				$elem = $elem . $n [0] . '" class="col-sm-2 control-label">';
				// agrega label
				$elem = $elem . $l . '</label><div class="col-sm-10" ' . $dId . '>';
				// agrega nombre campo
				switch ($t) {
					case "t" :
						if ($r == 1) {
							$elem = $elem . $this->tag->textField ( array (
									"$n[0]",
									"size" => 30,
									"class" => "form-control",
									"id" => "$n[0]",
									"readonly" => "",
									"data-bind" => "value: regexInput, valueUpdate: 'keyup'"
							) );
						} else {
							$elem = $elem . $this->tag->textField ( array (
									"$n[0]",
									"size" => 30,
									"class" => "form-control",
									"id" => "$n[0]",
									"data-bind" => "value: regexInput, valueUpdate: 'keyup'" 
							) );
						}
						break;
					case "tv" :
						if ($r == 1) {
							$elem = $elem . $this->tag->textField ( array (
									"$n[0]",
									"size" => 30,
									"class" => "form-control",
									"id" => "$n[0]",
									"value" => "$n[1]",
									"readonly" => "",
									"data-bind" => "value: regexInput, valueUpdate: 'keyup'" 
							) );
						} else {
							$elem = $elem . $this->tag->textField ( array (
									"$n[0]",
									"size" => 30,
									"class" => "form-control",
									"id" => "$n[0]",
									"value" => "$n[1]",
									"data-bind" => "value: regexInput, valueUpdate: 'keyup'" 
							) );
						}
						break;
					case "m" :
						$elem = $elem . $this->tag->textField ( array (
								"$n[0]",
								"size" => 30,
								"class" => "form-control number",
								"id" => "$n[0]",
								"value" => "$n[1]" 
						) );
						break;
					case "e" :
						$elem = $elem . $this->tag->textField ( array (
								"$n[0]",
								"size" => 30,
								"class" => "form-control email",
								"id" => "$n[0]" 
						) );
						break;
					case "p" :
						$elem = $elem . $this->tag->passwordField ( array (
								"$n[0]",
								"size" => 30,
								"class" => "form-control",
								"id" => "$n[0]" 
						) );
						break;
					case "d" :
						if (count ( $n ) > 1) {
							$elem = $elem . $this->tag->dateField ( array (
									"$n[0]",
									"min" => "0",
									"size" => 30,
									"class" => "form-control date datepicker",
									"id" => "$n[0]",
									"value" => $n[1]
									));
						}else{
							$elem = $elem . $this->tag->dateField ( array (
									"$n[0]",
									"min" => "0",
									"size" => 30,
									"class" => "form-control date datepicker",
									"id" => "$n[0]"
									));
						}
						break;
					case "sdb" :
						if (count ( $n ) > 3) {
							$elem = $elem . $this->tag->select ( array (
									"$n[0]",
									$n [1],
									"using" => $n [2],
									"class" => "form-control",
									"id" => "$n[0]",
									"value" => $n [3] 
							) );
						} else {
							$elem = $elem . $this->tag->select ( array (
									"$n[0]",
									$n [1],
									"using" => $n [2],
									"class" => "form-control",
									"id" => "$n[0]" 
							) );
						}
						break;
					case "sel" :
						if (count ( $n ) > 2) {
							$elem = $elem . $this->tag->select ( array (
									"$n[0]",
									$n [1],
									"class" => "form-control",
									"id" => "$n[0]",
									"value" => $n [2] 
							) );
						} else {
							$elem = $elem . $this->tag->select ( array (
									"$n[0]",
									$n [1],
									"class" => "form-control",
									"id" => "$n[0]" 
							) );
						}
						break;
					case "r" :
						foreach ( $n [1] as $rb ) {
							$elem = $elem . "<label for='$rb'>$rb</label>";
							$elem = $elem . $this->tag->radioField ( array (
									"$n[0]",
									"value" => "$rb",
									"id" => "$rb" 
							) );
							$elem = $elem . "&nbsp;";
						}
						break;
					case "ls" :
						$elem = $elem . $this->tag->textField ( array (
								"$n[0]",
								"size" => 30,
								"class" => "form-control",
								"id" => "$n[0]",
								"onkeyup" => "$n[1]"							
						) );
						$elem = $elem."</div><div id=\"livesearch\">";
						break;	
					case "cf" :
						$elem = $elem . $this->tag->checkField(array ( 
							"$n[0]",
							"value" => "$n[1]",
							"id" => "$n[0]",
							"onClick" => "$n[2]",
							"class" => "$n[3]"
						));
						break;
					case "tcb" :
						if ($r == 1) {
							$elem = $elem . $this->tag->textField ( array (
									"$n[0]",
									"size" => 30,
									"class" => "form-control $n[1]",
									"id" => "$n[0]",
									"readonly" => "",
									"data-bind" => "value: regexInput, valueUpdate: 'keyup'"
							) );
						} else {
							$elem = $elem . $this->tag->textField ( array (
									"$n[0]",
									"size" => 30,
									"class" => "form-control $n[1]",
									"id" => "$n[0]",
									"data-bind" => "value: regexInput, valueUpdate: 'keyup'"
							) );
						}
						break;
					case "tvcb" :
						if ($r == 1) {
							$elem = $elem . $this->tag->textField ( array (
									"$n[0]",
									"size" => 30,
									"class" => "form-control $n[2]",
									"id" => "$n[0]",
									"value" => "$n[1]",
									"readonly" => "",
									"data-bind" => "value: regexInput, valueUpdate: 'keyup'"
							) );
						} else {
							$elem = $elem . $this->tag->textField ( array (
									"$n[0]",
									"size" => 30,
									"class" => "form-control $n[2]",
									"id" => "$n[0]",
									"value" => "$n[1]",
									"data-bind" => "value: regexInput, valueUpdate: 'keyup'"
							) );
						}
						break;
				}
				$elem = $elem . '</div></div>';
		}
		return $elem;
	}
	
	public function form($campos, $action, $id = "id") {
		$form = $this->tag->form ( array (
				$action,
				"autocomplete" => "off",
				"class" => "form-horizontal",
				"id" => "$id" 
		) );
		foreach ( $campos as $c ) {
			if (count ( $c ) > 3) {
				$elem = ControllerBase::elemento ( $c [0], $c [1], $c [2], $c [3] );
			} else
				$elem = ControllerBase::elemento ( $c [0], $c [1], $c [2] );
			$form = $form . $elem;
		}
		
		$form = $form . $this->tag->endForm ();
		return $form;
	}
	
	/*
	 * 'enctype' => 'multipart/form-data'
	 */
	public function multiForm($campos, $action, $id = "id"){
		$form = $this->tag->form(
				array(
						$action,
						"autocomplete" => "off",
						"class" => "form-horizontal",
						'enctype' => 'multipart/form-data',
						"id" => "$id"
				)
		);
		foreach ($campos as $c){
			if(count($c) > 3){
				$elem = ControllerBase::elemento($c[0], $c[1], $c[2], $c[3]);
			}else $elem = ControllerBase::elemento($c[0], $c[1], $c[2]);
			$form = $form.$elem;
		}
	
		$form = $form.$this->tag->endForm();
		return $form;
	}
	
	public function thead($id, $head) {
		$tabla = '<div id="tdiv"><table id="' . $id . '" class="display" cellspacing="0"><thead><tr>';
		
		// Dibujar table head
		foreach ( $head as $h ) {
			$tabla = $tabla . '<th>' . $h . '</th>';
		}
		$tabla = $tabla . '</tr></thead><tbody>';
		return $tabla;
	}
	public function tbody($col) {
		$tr = "<tr>";
		$tr = $tr . $this->td ( $col );
		$tr = $tr . "</tr>";
		return $tr;
	}
	public function td($col) {
		$td = "";
		foreach ( $col as $c ) {
			$td = $td . '<td>' . $c . '</td>';
		}
		return $td;
	}
	public function ftable($tabla) {
		$tabla = $tabla . '</tbody></table></div>';
		return $tabla;
	}
	public function jsCargarDatos($campos, $hide = null, $show = null, $otros = null) {
		$js = "function cargarDatos(";
		foreach ( $campos as $c ) {
			$js = $js . $c . ",";
		}
		$js = rtrim ( $js, "," );
		$js = $js . "){";
		foreach ( $campos as $c2 ) {
			$js = $js . "$('#" . $c2 . "').val(" . $c2 . ");";
		}
		
		if ($hide != null) {
			foreach ( $hide as $h ) {
				$js = $js . "$('." . $h . "').hide();";
			}
		}
		if ($show != null) {
			foreach ( $show as $s ) {
				$js = $js . "$('." . $s . "').show();";
			}
		}
		if ($otros != null) {
			foreach ( $otros as $o ) {
				$js = $js . "$('#" . $o [0] . "').prop(" . $o [1] . ");";
			}
		}
		
		$js = $js . "}";
		return $js;
	}
	
	/*
	 * JS para submit de modificacion y cancelar
	 */
	public function jsBotones($form, $action1, $action2){
		$html = "
		function guardarCambio(){
			$('#$form').attr('action', '/cafeteria/$action1');
			$('#$form').submit();
		}
		function cancelar(){
			$('#$form').attr('action', '/cafeteria/$action2');
			$('#$form').submit();
		}
		";
		return $html;
	}
	
	/*
	 * Funcion para el dispatcher Forward
	 */
	public function forward($controller, $action) {
		return $this->dispatcher->forward ( array (
				"controller" => $controller,
				"action" => $action 
		) );
	}
	
	/*
	 * Funci�n para creaci�n de Links
	 */
	public function a($tipo, $accion, $label, $data = []){
		$a = "<a ";
		if ($tipo == 1){
			$a = $a."href='".$accion;
			if(count($data) > 0){
				$a = $a."?";
				foreach ($data as $d){
					$a = $a.$d[0]."=".$d[1]."&";
				}
				$a = rtrim($a, "&");
			}
			$a = $a."'>".$label;
		}else{
			$a = $a."onClick=\"".$accion."\">".$label;
		}
		$a = $a."</a>";
		return $a;
	}
	
	/*
	 * obtener var de post
	 */
	public function gPost($var){
		$v = $this->request->getPost($var);
		return $v;
	}
	
	/*
	 * Validar post
	 */
	public function vPost($var){
		$p = $this->gPost($var);
		if($p != null && $p != ""){
			return true;
		}
		return false;		
	}
	
	/*
	 * mensajes
	 */
	public function msg($mensaje, $tipo = "e"){
		switch ($tipo) {
			case "s":
				return $this->flash->success($mensaje);
				break;
			case "n":
				return $this->flash->notice($mensaje);
				break;
			case "w":
				return $this->flash->warning($mensaje);
				break;
			case "db":
				return $this->flash->error("Ocurri&oacute; un error durante la operaci&oacute;n");
				break;
			default:
				return $this->flash->error($mensaje);
				break;
		}
	}
	
	/*
	 * obtener var de request
	 */
	public function gReq($var){
		$v = $this->request->get($var);
		return $v;
	}
	
	/*
	 * obtener var de session
	 */
	public function gSession($var){
		$v = $this->session->get($var);
		return $v;
	}
	
	/*
	 * set var de session
	 */
	public function sSession($var, $valor){
		$v = $this->session->set($var, $valor);
		return $v;
	}
	
	/*
	 * Query generica
	 */
	public function query($modelo, $sql){
		// Execute the query
		return new Resultset(null, $modelo, $modelo->getReadConnection()->query($sql));
	}
	
	/*
	 * view function, sets the usual suspects that go into a view
	 */
	public function view($titulo, $form = "", $tabla = "", $botones = []){
		$this->view->titulo = $this->elemento("h1", ["titulo"], $titulo);
		$this->view->form = $form;
		$this->view->tabla = $this->ftable($tabla);
		$boton = "";
		if(count($botones) > 0){
			$boton = $this->elemento("bg", [["edit", "guardarCambio()", "Editar"],["cancel", "cancelar()", "Cancelar"]], "");
			$js = $this->jsCargarDatos($botones[0], ["main"], ["edit"], $botones[1]);
			$this->view->js = $js.$this->jsBotones($botones[2][0], $botones[2][1], $botones[2][2]);
		}
		$this->view->botones = $boton;
	}
	
	/*
	 * newPass: to generate initial generic password, in this example is pass
	 */
	public function newPass(){
		$pass = Parametros::findFirst("parametro = 'initialPass'");
		return $pass->valor;
	}
	
	/*
	 * checkPass: to compare passwords stored with encryption
	 */
	public function checkPass($pass, $comparePass = "", $initial = false){
		if ($initial){
			$p = Parametros::findFirst("parametro = 'initialPass'");
			return $this->security->checkHash($pass, $p->valor);
		}else{
			return $this->security->checkHash($pass, $comparePass);
		}
	}
	
	/*
	 * fecha + o - dias (d), meses (m), anios (y)
	 */
	public function datePlus($var, $option="d"){
		$date = date("Y-m-d");
		$resultado = $this->fechaHoy(false);
		switch ($option) {
			case "m":
				$mod_date = strtotime($date.$var." months");
				$resultado = date("Y-m-d",$mod_date);
				break;
			case "y":
				$mod_date = strtotime($date.$var." years");
				$resultado = date("Y-m-d",$mod_date);
				break;
			default:
				$mod_date = strtotime($date.$var." days");
				$resultado = date("Y-m-d",$mod_date);
				break;
		}
		return $resultado;
	}
	
	/*
	 * Limpiar campos
	 */
	public function limpiar(){
		$this->tag->resetInput();
	}
	
	/**
	 * Form con men� de cafeter�a
	 */
	public function formCafe($campos, $posMenu, $action, $id = "id") {
		$form = $this->tag->form ( array (
				$action,
				"autocomplete" => "off",
				"class" => "form-horizontal",
				"id" => "$id"
		) );
	
		//counter para la posici�n del men�
		$counter = 1;
		
		foreach ( $campos as $c ) {
			$elem = "";
			if($counter == $posMenu){
				$elem = $elem . ControllerBase::loadMenu ();
			}
			if (count ( $c ) > 3) {
				$elem = $elem . ControllerBase::elemento ( $c [0], $c [1], $c [2], $c [3] );
			} else
				$elem = $elem . ControllerBase::elemento ( $c [0], $c [1], $c [2] );
			$form = $form . $elem;
			$counter++;
		}
	
		$form = $form . $this->tag->endForm ();
		return $form;
	}
	
	public function loadMenu(){
		/*
	<div id="image_preview" class="row">  
    <div class='crop col-xs-12 col-sm-6 col-md-6 '>
         <img class="col-xs-12 col-sm-6 col-md-6" 
          id="preview0" src='img/preview_default.jpg'/>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6">
         more stuff
    </div>

</div> <div class='col-sm-12'>
		 */
		$hm = "<table id='tmenu' class='tmenu'><tbody>";
		//cada fila 4 columnas
		$cols = 6;
		$c = 1;
		//menu a cargar agrupado por secci�n
		$menu = Menu::find(["order" => "codigo"]);
		foreach ($menu as $m){
			if($c == 1){
				$hm = $hm."<tr>";
			}
			$elem = "<td><b>$m->nombre</b><br>";
			if($m->foto == "" || $m->foto == NULL){
				$elem = $elem . "<img id='$m->id' src='img/notFound.png'>";
			}else{
				$elem = $elem . "<img id='$m->id' src='img/$m->foto'>";
			}
			$elem = $elem. "<br><div class='mp'><a onClick =\"remHidden('$m->id');\" class=\"btn btn-info btn-lg\"><span class=\"glyphicon glyphicon-minus\"></span></a>";
			$elem = $elem. "<input type='text' id='d$m->id' class='txt'\>";
			$elem = $elem. "<a onClick =\"addHidden('$m->id');\" class=\"btn btn-info btn-lg\"><span class=\"glyphicon glyphicon-plus\"></span></a></div>";			
			$elem = $elem . $this->tag->hiddenField ( array (
					"n$m->id",
					"value" => 0
			) );
			$elem = $elem."</td>";			
			$hm = $hm.$elem;
			if($c == $cols){
				$hm = $hm."</tr>";
				$c = 1;
			}else{
				$c++;
			}			
		}
		if($c == 1){
			$hm = $hm."</tr>";
		}
		$hm = $hm."</tbody></table><br>";
		return $hm;
	}
	
	/**
	 * Form con men� de cocina
	 */
	public function formCocina($action, $id = "id") {
		$form = $this->tag->form ( array (
				$action,
				"autocomplete" => "off",
				"class" => "form-horizontal",
				"id" => "$id"
		) );
	
		$form = $form . $this->loadCocinaTotal();
	
		$form = $form . $this->tag->endForm ();
		return $form;
	}
	
	public function loadCocinaTotal(){
		$hm = "<div class='form-group main'><div class='col-sm-12' align='center' >";
		$hm = $hm . "<table id='tmenu' class='tmenu'><tbody>";
		//cada fila 4 columnas, se mostrar� item y luego total, as� emparejados por cada fila 2 items
		$cols = 4;
		$c = 1;
		//menu a cargar agrupado por secci�n
		//
		$menu = Menu::find(["order" => "seccion"]);
		$elem = "";
		foreach ($menu as $m){
			
			switch ($c){
				case 1:
					$hm = $hm."<tr>";
					//$elem = $elem."<td><b>$m->nombre</b></td>";
					$orden = Orden::find("hinicio > curdate() and estado < 3 order by prioridad desc");
					$total = 0;
					foreach ($orden as $o){
						$i = Item::findFirst("orden = $o->id and menu = $m->id");
						if($i != null){
							$total = $total + number_format($i->cantidad, 0);
						}
					}
                                        if($total > 0){
                                            $elem = $elem."<td><div class='mayor'><b>$m->nombre</b></div></td>"."<td><div class='mayor'><b>$total</b></div></td>";
                                        }else{
                                            $elem = $elem."<td><b>$m->nombre</b></td>"."<td><b>$total</b></td>";
                                        }					
					$c = $c+1;
					break;
				case 2:
					//$elem = $elem."<td><b>$m->nombre</b></td>";
					$orden = Orden::find("hinicio > curdate() and estado < 3 order by prioridad desc");
					$total = 0;
					foreach ($orden as $o){
						$i = Item::findFirst("orden = $o->id and menu = $m->id");
						if($i != null){
							$total = $total + number_format($i->cantidad, 0);
						}						
					}
					if($total > 0){
                                            $elem = $elem."<td><div class='mayor'><b>$m->nombre</b></div></td>"."<td><div class='mayor'><b>$total</b></div></td>";
                                        }else{
                                            $elem = $elem."<td><b>$m->nombre</b></td>"."<td><b>$total</b></td>";
                                        }
					$c = 1;
					$hm = $hm.$elem;
					$elem = "";
					break;
					
				
			}
			
			
		}
		
		$hm = $hm."</tbody></table></div></div><br>";
		return $hm;
	}
	
	/**
	 * Crear fila con clase espec�fica
	 * @param String $col
	 * @param String $class
	 * @return string
	 */
	public function tbodyClass($col, $class, $class2="" ) {
		$tr = "<tr class='$class $class2'>";
		$tr = $tr . $this->td ( $col );
		$tr = $tr . "</tr>";
		return $tr;
	}
        
        /*
         * Función para dibujar tabla para AJAX
         */
        public function ajaxTable($id, $columnas){
            $table = "<table id=\"$id\" class=\"display\" cellspacing=\"0\" width=\"95%\">\n<thead>\n<tr>\n";
            foreach ($columnas as $c) {
                $table = $table . "<th>$c</th>\n";
            }
            $table = $table . "</tr>\n</thead>\n<tfoot>\n<tr>\n";
            foreach ($columnas as $c) {
                $table = $table . "<th>$c</th>\n";
            }
            $table = $table . "</tr>\n</tfoot>\n</table>";
            return $table;
        }
        
        /*
	 * ajaxView function, sets the usual suspects that go into a view
	 */
	public function ajaxView($titulo, $form = "", $tabla = [], $botones = []){
		$this->view->titulo = $this->elemento("h1", ["titulo"], $titulo);
		$this->view->form = $form;
                if(count($tabla) > 0){
                    $this->view->tabla = $this->ajaxTable($tabla[0], $tabla[1]);
                }
		
		$boton = "";
		if(count($botones) > 0){
			$boton = $this->elemento("bg", [["edit", "guardarCambio()", "Editar"],["cancel", "cancelar()", "Cancelar"]], "");
			$js = $this->jsCargarDatos($botones[0], ["main"], ["edit"], $botones[1]);
			$this->view->js = $js.$this->jsBotones($botones[2][0], $botones[2][1], $botones[2][2]);
		}
		$this->view->botones = $boton;
	}
}
