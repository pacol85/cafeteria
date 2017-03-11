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
    	
		$form = parent::formCafe($campos, 3 , "orden/crear", "form1");
		
		//tabla
		$head = ["P","N&uacute;mero", "Orden", "Cambios", "Estado", "Acciones"];
		$tabla = parent::thead("orden", $head);
		$ordenes = Orden::find(["hinicio > curdate() and estado < 5", "order" => "prioridad desc"]);
		foreach ($ordenes as $o){
			$items = Item::find("orden = $o->id");
			$estado = Orderstatus::findFirst("id = $o->estado");
			$ordenado = "";
			foreach ($items as $i){
				$m = Menu::findFirst("id = $i->menu");
				$ordenado = $ordenado."$m->nombre: $i->cantidad, ";
			}
			$ordenado = substr($ordenado, 0, strlen($ordenado)-2);
			
			$accion = "";
			switch ($o->estado){
				case 3:
					$accion = parent::a(1, "orden/estadoOrden/$o->id", "Siguiente Paso") ." | ".
							parent::a(1, "orden/cancelarOrden/$o->id", "Cancelar");
				break;
				case 4:
					$accion = parent::a(1, "orden/prioridad/$o->id", "Prioridad");
				break;
				default:
					$accion = parent::a(1, "orden/cancelarOrden/$o->id", "Cancelar") ." | ".
					$accion = parent::a(1, "orden/prioridad/$o->id", "Prioridad");
				break;
			}

			$col = [
					$o->prioridad, 
					$o->numero, 
					$ordenado, 
					$o->otros,
					$estado->estado, 
					$accion		
			];
			if($o->prioridad > 0) {
				$tabla = $tabla.parent::tbodyClass($col, "prioridad");
			}else{
				$tabla = $tabla.parent::tbody($col);;
			}
			
		}
		
    	parent::view("Orden", $form, $tabla);
    }
    
    public function crearAction(){
    	if(parent::vPost("numero")){
    		$num = parent::gPost("numero");
    		$exist = Orden::find("numero = '$num' and hinicio > curdate()");
    		if(count($exist) > 0){
    			parent::msg("Este n&uacute;mero de orden ya fue ingresado");
    			return parent::forward("orden", "index");
    		}
    		
    		$orden = new Orden();
    		$orden->cliente = parent::gPost("cliente");
    		$orden->estado = 1;
    		$orden->hinicio = parent::fechaHoy(true);
    		$orden->identificacion = parent::gPost("ident");
    		$orden->numero = $num;
    		$orden->otros = parent::gPost("otros");
    		$orden->prioridad = 0;
    		    		
    		if($orden->save()){
    			$items = 0;
    			//guardar items de la orden
    			$menu = Menu::find();
    			foreach ($menu as $m){
    				$cant = parent::gPost("n$m->id");
    				if($cant != null && $cant > 0){
    					$items++;
    					$i = new Item();
    					$i->cantidad = $cant;
    					$i->menu = $m->id;
    					$i->orden = $orden->id;
    					if(!$i->save()){
    						parent::msg("", "db");
    					}
    				}    				 
    			}
    			if($items < 1){
    				$orden->delete();
    				parent::msg("La orden no conten&iacute;a ning&uacute;n &iacute;tem");	
    			}else{
    				parent::msg("Orden creada exitosamente", "s");
    			}    			
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operación");
    		}
    	}else{
    		parent::msg("El n&uacute;mero de orden no puede quedar en blanco");
    	}
    	parent::forward("orden", "index");
    }
    
    /**
     * CAmbiar estado a Entregado
     * @param id de orden $oid
     */
    function estadoOrdenAction($oid){
    	$orden = Orden::findFirst("id = $oid");
    	$orden->estado = $orden->estado + 1;
    	$orden->hfinal = parent::fechaHoy(true);
    	if($orden->update()){
    		parent::msg("Orden $orden->numero entregada al cliente", "s");
    		return parent::forward("orden", "index");
    	}
    }
    
    /**
     * Cambiar estado a cancelado
     * @param id de orden $oid
     */
    function cancelarOrdenAction($oid){
    	$orden = Orden::findFirst("id = $oid");
    	$orden->estado = 5;
    	$orden->hfinal = parent::fechaHoy(true);
    	if($orden->update()){
    		parent::msg("Orden $orden->numero cancelada", "s");
   			return parent::forward("orden", "index");
    	}
    }
    
    /**
     * CAmbiar prioridad
     * @param id de orden $oid
     */
    function prioridadAction($oid){
    	$orden = Orden::findFirst("id = $oid");
    	$prioridadMax = Orden::maximum(["column" => "prioridad", "conditions" => "hinicio > curdate() and estado < 5"]);
    	$orden->prioridad = $prioridadMax + 1;
    	if($orden->update()){
    		parent::msg("Se modific&oacute; prioridad de orden: $orden->numero", "n");
    		return parent::forward("orden", "index");
    	}
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
    
    public function cocinaAction()
    {
    	$form = parent::formCocina("orden/cocinados", "form1");
    
    	//tabla
    	$head = ["P", "N&uacute;mero", "Orden", "Cambios", "Estado", "Acciones"];
    	$tabla = parent::thead("tordenes", $head);
    	$ordenes = Orden::find("hinicio > curdate() and estado < 3 order by prioridad desc");
    	$pos = 1;
    	foreach ($ordenes as $o){
    		$items = Item::find("orden = $o->id");
    		$ordenado = "";
    		$estado = Orderstatus::findFirst("id = $o->estado");
    		foreach ($items as $i){
    			$m = Menu::findFirst("id = $i->menu");
    			$ordenado = $ordenado."$m->nombre: $i->cantidad, ";
    		}
    		$ordenado = substr($ordenado, 0, strlen($ordenado)-2);
    		$col = [
    		$o->prioridad, 
    		$o->numero,
    		$ordenado,
    		$o->otros,
    		$estado->estado,
    		parent::a(1, "orden/estadoCocina/$o->id", "Siguiente Paso")
    		];
    		switch ($pos) {
    			case 1:
    				$tabla = $tabla.parent::tbodyClass($col, "uno");
    				break;
    			case 2:
    				$tabla = $tabla.parent::tbodyClass($col, "dos");
    				break;
    			default:
    				$tabla = $tabla.parent::tbody($col);;
    				break;
    		}
    		$pos = $pos + 1;
    		
    	}
    
    	parent::view("Cocina", $form, $tabla);
    }
    
    function estadoCocinaAction($oid){
    	$orden = Orden::findFirst("id = $oid");
    	$orden->estado = $orden->estado + 1;
    	if($orden->update()){
    		if($orden->estado == 3){
    			parent::msg("Orden $orden->numero cocinada y entregada a caja", "s");
    		}
    		return parent::forward("orden", "cocina");
    	}
    }
}