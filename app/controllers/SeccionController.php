<?php
class SeccionController extends ControllerBase
{
    public function indexAction()
    {
		parent::limpiar();
    	$campos = [
    			["t", ["nombre"], "Nombre"],
				["h", ["id"], ""],
				["t", ["desc"], "Descripci&oacute;n"],
				["s", ["guardar"], "Guardar"]
		];
		$head = ["Nombre", "Disponible", "Acciones"];
		$tabla = parent::thead("seccion", $head);
		$seccion = Seccion::find();
		foreach ($seccion as $s){
			$tabla = $tabla.parent::tbody([
					$s->nombre,
					$s->desc,
					parent::a(2, "cargarDatos('".$s->id."','".$s->nombre."','".
							$s->desc."');", "Editar")." | ".
					parent::a(1,"seccion/eliminar/$s->id", "Eliminar")
			]);
		}
		
		//js
		$fields = ["id", "nombre", "desc"];
		$otros = "";
		$jsBotones = ["form1", "seccion/edit", "seccion"];
		
		
    	$form = parent::form($campos, "seccion/guardar", "form1");
    	$tabla = parent::ftable($tabla);
    
    	parent::view("Secciones de Men&uacute;", $form, $tabla, [$fields, $otros, $jsBotones]);
    }
    
    public function guardarAction(){
    	if(parent::vPost("nombre")){
    		$nombre = parent::gPost("nombre");
    		$exist = Seccion::find("nombre = '$nombre'");
    		if(count($exist) > 0){
    			parent::msg("La secci&oacute;n ingresada ya existe");
    			return parent::forward("seccion", "index");
    		}
    		$s = new Seccion();
    		$s->nombre = $nombre;
    		$s->desc = parent::gPost("desc");
    		if($s->save()){
    			parent::msg("Secci&oacute;n creada exitosamente", "s");
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operación");
    		}
    	}else{
    		parent::msg("El campo Nombre no puede quedar en blanco");
    	}
    	parent::forward("seccion", "index");
    }
    
    public function eliminarAction($id){
    	$s = Seccion::findFirst("id = ".$id);
    	$menus = Menu::find("seccion = $s->id");
    	if(count($menus) > 0){
    		parent::msg("No se puede eliminar una Secci&oacute;n que tenga asociado uno o m&aacute;s platos", "w");
    		return parent::forward("seccion", "index");
    	}
    	$seccion = $s->nombre;    		 
    		if($s->delete()){
    			parent::msg("Se elimin&oacute; la Secci&oacute;n: $seccion", "s");
    		}else{
    			parent::msg("","db");
    		}
    	parent::forward("seccion", "index");
    }
    public function editAction(){
    	if(parent::vPost("nombre") && parent::vPost("id")){
    		$id = parent::gPost("id");
    		$nombre = parent::gPost("nombre");
    		$exist = Seccion::find("nombre = '$nombre' and not (id = $id)");
    		if(count($exist) > 0){
    			parent::msg("La secci&oacute;n ingresada ya existe");
    			return parent::forward("seccion", "index");
    		}
    		$s = Seccion::findFirst("id = ".$id);
    		$s->nombre = $nombre;
    		$s->desc = parent::gPost("desc");
    		if($s->save()){
    			parent::msg("Secci&oacute;n editada exitosamente", "s");
    		}else{
    			parent::msg("","db");
    		}
    	}else{
    		parent::msg("El campo Nombre no puede quedar en blanco");
    	}
    	parent::forward("seccion", "index");
    }
}