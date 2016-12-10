<?php
class MenuController extends ControllerBase
{
    public function indexAction()
    {
		parent::limpiar();
		$secciones = Seccion::find();
    	$campos = [
    			["sdb", ["seccion", $secciones, ["id", "nombre"]], "Seccion"],
    			["t", ["codigo"], "C&oacute;digo"],
				["t", ["nombre"], "Nombre"],
				["h", ["id"], ""],
				["t", ["desc"], "Descripci&oacute;n"],
				["m", ["precio", 0], "Precio"],
    			["f", ["foto"], "Foto"],
				["s", ["guardar"], "Guardar"]
		];
		$head = ["Secci&oacute;n", "C&oacute;digo", "Nombre", "Precio","Disponible", "Acciones"];
		$tabla = parent::thead("menu", $head);
		$menu = Menu::find();
		
		foreach ($menu as $m){
			$s = Seccion::findFirst("id = ".$m->seccion);
			$disp = "S&iacute;";
			if($m->disponible != 1){
				$disp = "No";
			}
			$tabla = $tabla.parent::tbody([
					$s->nombre,
					$m->codigo,
					$m->nombre,
					$m->precio,
					parent::a(1,"menu/disponible/$m->id", $disp),
					parent::a(2, "cargarDatos('".$m->id."','".$m->seccion."','".$m->codigo."','".$m->nombre."','".
							$m->descripcion."','".$m->precio."');", "Editar")." | ".					
					parent::a(1,"menu/eliminar/$m->id", "Eliminar")
			]);
		}
		
		//js
		$fields = ["id", "seccion", "codigo", "nombre", "descripcion", "precio"];
		$otros = "";
		$jsBotones = ["form1", "menu/edit", "menu"];
		
		
    	$form = parent::multiForm($campos, "menu/guardar", "form1");
    	$tabla = parent::ftable($tabla);
    
    	parent::view("Men&uacute;", $form, $tabla, [$fields, $otros, $jsBotones]);
    }
    
    public function guardarAction(){
    	if(parent::vPost("codigo")){
    		$cod = parent::gPost("codigo");
    		$exist = Menu::find("codigo = '$cod'");
    		if(count($exist) > 0){
    			parent::msg("El c&oacute;digo ingresado ya existe");
    			return parent::forward("menu", "index");
    		}
    		$nombre = parent::gPost("nombre");
    		
    		$menu = new Menu();
    		$menu->codigo = $cod;
    		$menu->descripcion = parent::gPost("desc");
    		$menu->disponible = 1;
    		$menu->nombre = $nombre;
    		$menu->precio = parent::gPost("precio");
    		$menu->seccion = parent::gPost("seccion");
    		
    		//Phalcon upload file
    		if (true == $this->request->hasFiles() && $this->request->isPost()) {
    			$upload_dir = APP_PATH . '\\public\\img\\';
    		
    			foreach ($this->request->getUploadedFiles() as $file) {
    				$file->moveTo($upload_dir . $file->getName());
    				$menu->foto = $file->getName();
    			}
    		
    		}
    		
    		/*if($this->request->hasFiles())
    		{
    			$destination = APP_PATH . '\\public\\img\\';
    			$pictureHandler = new Sirius\Upload\Handler($destination);
    			$pictureHandler->addRule(Sirius\Upload\Handler::RULE_IMAGE, array('allowed' => array('jpg', 'jpeg', 'png')));
    			$upload = new Sirius\Upload\HandlerAggregate();
    			$upload->addHandler('picture', $pictureHandler);
    			$result = $upload->process($this->request->getUploadedFiles()); 
				
    			if ($result) { 
    				foreach ($result as $key => $file) {
    					if (!($file->isValid())) {
    						parent::msg(implode(", ", $file->getMessages()));
    					}else{
    						$menu->foto = $file->name;
    					}
    				}
    			}
    		}
    		*/
    		if($menu->save()){
    			parent::msg("Men&uacute; creado exitosamente", "s");
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operación");
    		}
    	}else{
    		parent::msg("El campo c&oacute; no puede quedar en blanco");
    	}
    	parent::forward("menu", "index");
    }
    
    public function eliminarAction($id){
    	$menu = Menu::findFirst("id = $id");
    	$items = Item::find("menu = $id");
    	if(count($items) > 0){
    		parent::msg("No se puede eliminar un Item que est&eacute; asociado a una orden", "w");
    	}else {
    		$nMenu = $menu->nombre;    		 
    		if($menu->delete()){
    			parent::msg("Se elimin&oacute; el Item de Men&uacute;: $nMenu", "s");
    		}else{
    			parent::msg("","db");
    		}
    	}    	
    	parent::forward("menu", "index");
    }
    
    public function disponibleAction($id){
    	$menu = Menu::findFirst("id = $id");
    	if($menu->disponible == 0){
    		$menu->disponible = 1;
    	}else{
    		$menu->disponible = 0;
    	}
    	
    	if($menu->update()){
    		parent::msg("Se cambio estado de disponibilidad de Item: $menu->nombre", "s");
    	}else{
    		parent::msg("","db");
    	}
    	parent::forward("menu", "index");
    }
    
    public function editAction(){
    	if(parent::vPost("id")){
    		$bancos = Bancos::findFirst("id = ".parent::gPost("id"));
    		$bancos->nombre = parent::gPost("nombre");
    		$bancos->telefono = parent::gPost("telefono");
    		$bancos->direccion = parent::gPost("direccion");
    		if($bancos->update()){
    			parent::msg("Banco modificado exitosamente", "s");
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operaci&oacute;n");
    		}
    	}else{
    		parent::msg("Ocurri&oacute; un error al cargar los Bancos");
    	}
    	parent::forward("bancos", "index");
    }
}