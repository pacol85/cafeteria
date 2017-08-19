<?php
class OrdenController extends ControllerBase
{
    public function indexAction()
    {
		parent::limpiar();
		$campos = [
    			["t", ["numero"], "N&uacute;mero"],
			["t", ["cliente"], "Cliente"],
    			["t", ["mhora"], "Hora"],    			
    			["t", ["otros"], "Cambios"],
                        ["h", ["id"], ""],
    			["s", ["crear"], "Crear"]
    	];
    	
		$form = parent::formCafe($campos, 3 , "orden/crear", "form1");
		
		//tabla
		/*$head = ["P","N&uacute;mero", "Orden", "Hora", "Cambios", "Estado", "Acciones"];
		$tabla = parent::thead("orden", $head);
		$ordenes = Orden::find(["hinicio > curdate() and estado < 4", "order" => "prioridad desc"]);
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
					$accion = parent::a(1, "orden/prioridad/$o->id", "Prioridad") ." | ".
					$accion = parent::a(1, "orden/entregado/$o->id", "Entregado");
				break;
			}

			$col = [
					$o->prioridad, 
					$o->numero, 
					$ordenado,
                            $o->identificacion, //identificación es mhora
					$o->otros,
					$estado->estado, 
					$accion		
			];
			if($o->prioridad > 0) {
				$tabla = $tabla.parent::tbodyClass($col, "prioridad");
			}else{
				$tabla = $tabla.parent::tbody($col);;
			}
			
		}*/
                
        //js
        $fields = ["id", "numero", "cliente", "mhora", "otros"];
        $otros = "";
        $jsBotones = ["form1", "orden/edit", "orden"];
		
    	parent::view("Orden", $form, "", [$fields, $otros, $jsBotones]); //, $tabla);
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
    		$orden->identificacion = parent::gPost("mhora");
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
                                    
                                    //validación si es un combo para seleccionar los items incluidos en su lugar
                                    $combo = substr($m->codigo, 0, 1);
                                    if($combo == "C"){
                                        $success = true;
                                        switch ($m->id) {
                                            case 41:
                                                $success = $this->addItem($cant, 2, $orden->id); //hamburguesa
                                                $success = $this->addItem($cant, 13, $orden->id); //papas
                                                $items++;
                                                break;
                                            case 42:
                                            case 44:
                                            case 49:
                                                $success = $this->addItem($cant, 5, $orden->id); //Carne a la plancha
                                                break;
                                            case 43:
                                            case 47:
                                            case 52:
                                                $success = $this->addItem($cant, 24, $orden->id); //pollo a la plancha
                                                break;
                                            case 45:
                                            case 51:
                                                $success = $this->addItem($cant, 9, $orden->id); //plato del día
                                                break;
                                            case 46:
                                            case 50:
                                                $success = $this->addItem($cant, 5, $orden->id); //hamburguesa
                                                break;
                                            case 48:
                                                $success = $this->addItem($cant, 7, $orden->id); //hamburguesa
                                                break;
                                        }
                                        if($success){
                                            $items++;
                                        }else{
                                            parent::msg("", "db");
                                        }
                                        
                                    }else{
                                        if(!$this->addItem($cant, $m->id, $orden->id)){
                                            parent::msg("", "db");
                                        }else{
                                            $items++;
                                        }
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
    			parent::msg("Ocurri&oacute; un error durante la operaci�n");
    		}
    	}else{
    		parent::msg("El n&uacute;mero de orden no puede quedar en blanco");
    	}
    	parent::forward("orden", "index");
    }
    
    /**
     * Agregar item a la orden
     */
    public function addItem($cant, $mid, $oid){
        $i = new Item();
        $i->cantidad = $cant;
        $i->menu = $mid;
        $i->orden = $oid;
        $result = true; //si hay exito
        if(!$i->save()){
            $result = false;
        }
        return $result;
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
     * Cambiar estado a entregado
     * @param id de orden $oid
     */
    function entregadoAction($oid){
    	$orden = Orden::findFirst("id = $oid");
    	$orden->estado = 4;
    	$orden->hfinal = parent::fechaHoy(true);
    	if($orden->update()){
    		parent::msg("Orden $orden->numero entregada al cliente", "s");
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
    	if(parent::vPost("numero")){
    		$num = parent::gPost("numero");
                $id = parent::gPost("id");
    		$exist = Orden::find("numero = '$num' and hinicio > curdate() and id not like $id");
    		if(count($exist) > 0){
    			parent::msg("Este n&uacute;mero de orden ya fue ingresado");
    			return parent::forward("orden", "index");
    		}
    		
    		$orden = Orden::findFirst("id = $id");
    		$orden->cliente = parent::gPost("cliente");
    		$orden->estado = 1;
    		$orden->hinicio = parent::fechaHoy(true);
    		$orden->identificacion = parent::gPost("mhora");
    		$orden->numero = $num;
    		$orden->otros = parent::gPost("otros");
    		$orden->prioridad = 0;
    		    		
    		if($orden->update()){
                    //borrar otros items anteriores
                    $oldItems = Item::find("orden = $orden->id");
                    foreach ($oldItems as $oi){
                        $oi->delete();
                    }
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
    				parent::msg("Orden modificada exitosamente", "s");
    			}    			
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operaci�n");
    		}
    	}else{
    		parent::msg("El n&uacute;mero de orden no puede quedar en blanco");
    	}
    	parent::forward("orden", "index");
    }
    
    public function cocinaAction()
    {
    	$form = parent::formCocina("orden/cocinados", "form1");
        //$tabla = ["tordenes", ["P", "N&uacute;mero", "Orden", "Hora", "Cambios", "Estado"]];
        
    	//tabla
    	/*$head = ["P", "N&uacute;mero", "Orden", "Cambios", "Estado", "Acciones"];
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
                $cl2 = "";
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
    		
    	}*/
    
    	parent::view("Cocina", $form);
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
    
    /**
     * Función para cargar la tabla de la Cocina con JSON
     */
    function tablaCocinaAction() {
        $ordenes = Orden::find("hinicio > curdate() and estado < 3 order by prioridad desc");
        
        foreach ($ordenes as $o) {
            //$tiempos = "";
            if($o->identificacion != null && $o->identificacion != "00:00:00"){
                if(parent::comparaTiempo($o->identificacion) && $o->prioridad < 1){
                    $this->prioridad($o->id);
                }/*else{
                    $timezone = - 6;
                    date_default_timezone_set('America/El_Salvador');
                    //(time() - (30 * 60)) < strtotime($tiempo) && time() > strtotime($tiempo)
                    $ctime = date('H:i', time()+(30*60));
                    $stime = date('H:i', strtotime($o->identificacion));
                    $tiempos = "hora -30min:".$ctime." vs hora guardada: ".$stime;
                }*/   
            }            
            $items = Item::find("orden = $o->id");
            $ordenado = "";
            $e = Orderstatus::findFirst("id = $o->estado");
            foreach ($items as $i){
                    $m = Menu::findFirst("id = $i->menu");
                    $ordenado = $ordenado."$m->nombre: $i->cantidad, ";
            }
            $ordenado = substr($ordenado, 0, strlen($ordenado)-2);
            $stime = "";
            if($o->identificacion != "00:00:00"){
                $stime = date('H:i', strtotime($o->identificacion));
            }            
            $response["data"][] = ["p" => $o->prioridad, "n" => $o->numero, "o" => $ordenado, "h" =>$stime, "c" => $o->otros , "e" => $e->estado];            
        }
        return parent::sendJson($response);
    }
    
    /**
     * Función para actualizar totales de ordenes
     */
    function totalesCocinaAction(){
        
        $nt = 0;
        $cid = parent::gPost("cid");
        $biggest = 0;
        $o = Orden::find("hinicio > curdate() and estado < 3");
        $form2 = [];
        $lid = "";
        if($cid == ""){
            
            $nt = count($o);
            
            if($nt > 0){
                foreach ($o as $o2) {
                    if($o2->id > $biggest) $biggest = $o2->id;
                }
                $lid = $biggest;
            }else{
                $lid = "";
            }
        }else{
            $biggest = $cid;
            foreach ($o as $o2) {
                if($o2->id > $biggest){
                    $biggest = $o2->id;
                    $nt = $nt + 1;
                }
            }
            $lid = $biggest;
        }
        $form = ["tots" => parent::formCocina("orden/cocinados", "form1"), "nt" => "$nt", "lid" => $lid];
        return parent::sendJson($form);
    }
    
    /**
     * CAmbiar prioridad2
     * @param id de orden $oid
     */
    function prioridad($oid){
    	$orden = Orden::findFirst("id = $oid");
    	$prioridadMax = Orden::maximum(["column" => "prioridad", "conditions" => "hinicio > curdate() and estado < 5"]);
    	$orden->prioridad = $prioridadMax + 1;
        $orden->update();
    }
    
    /**
     * Función para cargar la tabla de las Ordenes con JSON
     */
    function tablaOrdenAction() {
        $ordenes = Orden::find(["hinicio > curdate() and estado < 4", "order" => "prioridad desc"]);
        foreach ($ordenes as $o){
            $items = Item::find("orden = $o->id");
            $estado = Orderstatus::findFirst("id = $o->estado");
            $ordenado = "";
            $ids = "";
            foreach ($items as $i){
                    $m = Menu::findFirst("id = $i->menu");
                    $ordenado = $ordenado."$m->nombre: $i->cantidad, ";
                    $ids = $ids."$m->id,$i->cantidad;";
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
                            $accion = parent::a(1, "orden/prioridad/$o->id", "Prioridad") ." | ".
                            $accion = parent::a(2, "cargarDatos('".$o->id."', '".$o->numero."', '".$o->cliente."', '".$o->identificacion
                                    ."', '".$o->otros."');fillMenu('".$ids."');","Editar") ." | ".
                            $accion = parent::a(1, "orden/entregado/$o->id", "Entregado");
                    break;
            }

            $stime = "";
            if($o->identificacion != "00:00:00"){
                $stime = date('H:i', strtotime($o->identificacion));
            }
            $response["data"][] = ["p" => $o->prioridad, "n" => $o->numero, "o" => $ordenado, "h" =>$stime, "c" => $o->otros , "e" => $estado->estado, "a" => $accion];            
        }
        return parent::sendJson($response);
    }
}