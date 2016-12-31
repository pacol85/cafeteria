<?php
class OrdenController extends ControllerBase
{
    public function indexAction()
    {
		parent::limpiar();
		$campos = [
    			["t", ["numero"], "N&uacute;mero"],
				["t", ["cliente"], "Cliente"],
    			["t", ["ident"], "Descripci&oacute;n"],    			
    			["t", ["otros"], "Cambios"],
    			["s", ["crear"], "Crear"]
    	];
    	
		$form = parent::formCafe($campos, 4 , "orden/crear", "form1");
    	parent::view("Orden", $form);
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
    				if(strlen($file->getName()) > 0){
    					$punto = strpos($file->getName(), ".");
    					$menu->foto = $menu->codigo.substr($file->getName(), $punto);
    					$file->moveTo($upload_dir . $menu->foto);    					
    				}    				
    			}    		
    		}  		
    		
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
    	if(parent::vPost("codigo")){
    		$cod = parent::gPost("codigo");
    		$id = parent::gPost("id");
    		$exist = Menu::find("codigo = '$cod' and not(id = $id)");
    		if(count($exist) > 0){
    			parent::msg("El c&oacute;digo ingresado ya existe");
    			return parent::forward("menu", "index");
    		}
    		$nombre = parent::gPost("nombre");
    		
    		$menu = Menu::findFirst("id = $id");
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
    				if(strlen($file->getName()) > 0){
    					$punto = strpos($file->getName(), ".");
    					$menu->foto = $menu->codigo.substr($file->getName(), $punto);
    					$file->moveTo($upload_dir . $menu->foto);
    					
    				}
    				
    			}
    		
    		}
    		
    		if($menu->update()){
    			parent::msg("Men&uacute; actualizado exitosamente", "s");
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operación");
    		}
    	}else{
    		parent::msg("El campo c&oacute; no puede quedar en blanco");
    	}
    	parent::forward("menu", "index");
    }
}