<?php
class ReporteController extends ControllerBase
{
    public function indexAction()
    {
        parent::limpiar();       
        parent::view("Reportes", ""); //, $tabla);
        $this->view->form = parent::elemento("h", ["hdias"], 1);
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
    			parent::msg("Ocurri&oacute; un error durante la operaci�n");
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
    
    /**
     * chartData
     */
    function chartDataAction(){
        $days = parent::gPost("days");
        $query = "select * from orden where hinicio > curdate() - interval $days day and (estado = 1 or estado = 4)";
        $ordenes = parent::query(new Orden(), $query);
        $response = array();
        $totales["0"] = 0;
        foreach ($ordenes as $o){
            $items = Item::find("orden = $o->id");
            foreach ($items as $i) {
                if($totales["$i->menu"] != null){
                    $totales["$i->menu"] = $totales["$i->menu"] + $i->cantidad;
                }else{
                    $totales["$i->menu"] = $i->cantidad + 0;
                }                
            }
        }
        
        arsort($totales);
        
        foreach ($totales as $k => $t){
            //$prueba = $prueba."$t, $k; ";
            $menu = Menu::findFirst("id = $k");
            if($k != 0) array_push($response, array('label' => "$menu->nombre", 'value' => $t));
        }
        /*
        $response = array(
      array('label' => 'Hamburguesas','value' => 4),
      array('label' => 'Papas_fritas','value' => 8)
      
);*/
        return parent::sendJson($response);
    }
    
    /**
     * chartData
     */
    function chartDataAPIAction($days){
        //$days = parent::gPost("days");
        $query = "select * from orden where hinicio > curdate() - interval $days day and (estado = 1 or estado = 4)";
        $ordenes = parent::query(new Orden(), $query);
        $response = array();
        $totales["0"] = 0;
        foreach ($ordenes as $o){
            $items = Item::find("orden = $o->id");
            foreach ($items as $i) {
                if($totales["$i->menu"] != null){
                    $totales["$i->menu"] = $totales["$i->menu"] + $i->cantidad;
                }else{
                    $totales["$i->menu"] = $i->cantidad +0;
                }                
            }
        }
        
        arsort($totales);
        
        foreach ($totales as $k => $t){
            //$prueba = $prueba."$t, $k; ";
            $menu = Menu::findFirst("id = $k");
            if($k != 0) array_push($response, array('value' => $t, 'label' => "$menu->nombre"));
        }
        /*
        $response = array(
      array('label' => 'Hamburguesas','value' => 4),
      array('label' => 'Papas_fritas','value' => 8)
      
);*/
        return parent::sendJson($response);
    }
    
    
    /**
     * chartDataAPIAction
     * $days --> integer para días atras para la solicitud
     */
    function chartDataAPIV2Action($days){
        //$days = parent::gPost("days");
        $query = "select * from orden where hinicio > curdate() - interval $days day and (estado = 1 or estado = 4)";
        $ordenes = parent::query(new Orden(), $query);
        $response = array();
        $totales["0"] = 0;

        foreach ($ordenes as $o){
            $items = Item::find("orden = $o->id");
            foreach ($items as $i) {
                if($totales["$i->menu"] != null){
                    $totales["$i->menu"] = $totales["$i->menu"] + $i->cantidad;
                }else{
                    $totales["$i->menu"] = $i->cantidad +0;
                }
            }
        }

        arsort($totales);
        $suma = $this->getSuma($totales);

        $increments = count($totales);

        $hs = $this->hexSplit($increments);
        $iter = 0;
        $lastItem = [];
        
        foreach ($totales as $k => $t){
            //$prueba = $prueba."$t, $k; ";
            $menu = Menu::findFirst("id = $k");
            $porc = ($t / $suma) * 100; 
            
            $itemData = $this->angles($porc, $lastItem, $iter);
            if($k != 0) array_push($response, array('value' => $t, 'label' => "$menu->nombre", 
                'porc' => "$porc", 'color' => "#$hs[$iter]", 'idata' => $itemData));
            
            $iter = $iter + 1;
            
            //save lastItem
            $lastItem = $itemData;
        }
        
        array_push($response, $hs);
        return parent::sendJson($response);
    }
    
    /**
     * hexSplit
     */
    public function hexSplit($increments, $start = "111111", $end = "eeeeee"){
        $s = hexdec($start);
        $e = hexdec($end);
        $n = abs($e - $s);
        $d = $increments;
        
        $tope = $e;
        
        if($n < $d){
            $d = $n;
        }
        
        $dif = round($n/$d);
        $a = [];
        for($i = 0; $i < $d; $i++){
            $tope = $tope - $dif;
            $hex = dechex($tope);
            array_push($a, $hex);
        }
        
        return $a;
    }
    
    /**
     * 
     * @param type $totales
     * @return type
     */
    public function getSuma($totales){
        $suma = 0;
        foreach ($totales as $t){
            $suma = $suma + $t;
        }
        return $suma;
    }
    
    /**
     * 
     * @param type $porc
     */
    public function angles($porc, $lastItem, $pos = 0){
        
        $wheelRotate = 0;
        
        $angle = ($porc / 100) * 360;
        
        if($pos == 0){
            $wheelRotate = $angle / 2 + 90;
        }else{
            $wheelRotate = ($angle / 2) + ($lastItem->angle / 2);
        }
        
        $startAngle = $lastItem["startAngle"] - $angle;
        $endAngle = $lastItem["startAngle"];       
        
        if($endAngle == null){
            $endAngle = 0;
        }
        
        $item = ["startAngle" => $startAngle, "endAngle" => $endAngle, "rotation" => $wheelRotate];
        
        return $item;
    }
}