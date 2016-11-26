<?php
class MenuController extends ControllerBase
{
    public function indexAction()
    {
		parent::limpiar();
    	$campos = [
    			["t", ["codigo"], "C&oacute;digo"],
				["t", ["nombre"], "Nombre"],
				["h", ["id"], ""],
				["t", ["desc"], "Descripci&oacute;n"],
				["m", ["precio", 0], "Precio"],
    			["f", ["foto"], "Foto"],
				["s", ["guardar"], "Guardar"]
		];
		$head = ["C&oacute;digo", "Nombre", "Precio","Disponible", "Acciones"];
		$tabla = parent::thead("menu", $head);
		$menu = Menu::find();
		foreach ($menu as $m){
			$tabla = $tabla.parent::tbody([
					$m->codigo,
					$m->nombre,
					$m->precio,
					$m->disponible,
					parent::a(2, "cargarDatos('".$m->id."','".$m->codigo."','".$m->nombre."','".
							$m->descripcion."','".$m->precio."');", "Editar")." | ".
					parent::a(1,"menu/eliminar", "Eliminar", [["id", $m->id]])
			]);
		}
		
		//js
		$fields = ["id", "codigo", "nombre", "descripcion", "precio"];
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
    		if($this->request->hasFiles())
    		{
    			foreach($this->request->getUploadedFiles() as $file)
    			{
    				try {
    					
    				}catch(Exception $e) {
						parent::msg('Error loading file "'.$file->getTempName().'": '.$e->getMessage());
    				}					
				}
    			
    		}
    		$menu = new Menu();
    		$menu->codigo = $cod;
    		$menu->descripcion = parent::gPost("desc");
    		$menu->disponible = 1;
    		$menu->nombre = parent::gPost("menu");
    		$menu->precio = parent::gPost("precio");
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
    
    public function eliminarAction(){
    	$bancos = Bancos::findFirst("id = ".parent::gReq("id"));
    	$cheques = Cheques::find(array("banco = $bancos->id"));
    	if(count($cheques) > 0){
    		parent::msg("No se puede eliminar un Banco que tenga asociado uno o m&aacute;s cheques", "w");
    	}else {
    		$nBancos = $bancos->nombre;    		 
    		if($bancos->delete()){
    			parent::msg("Se elimin&oacute; el Banco: $nBancos", "s");
    		}else{
    			parent::msg("","db");
    		}
    	}    	
    	parent::forward("bancos", "index");
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