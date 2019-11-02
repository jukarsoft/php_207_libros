<?php
	session_start();

	$titulo=null;
	$precio=null;
	$resultado=null;
	$datosLibreria=null;
	//ver de cambiar $_SESION 
	if (isset($_SESSION['libros'])) {
		$libreria = $_SESSION['libros'];
	} else {
	 	$libreria = array();
	}



	if (isset($_POST['alta'])) {
		$titulo=$_POST['titulo']; 
		$precio=$_POST['precio']; 
		if (trim($titulo)=='') {
			$resultado.="-Titulo no informado";
		}
		if (trim($precio)=='' || !is_numeric($precio)) {
			$resultado.=" -Precio no informado o no es un numérico";
		}
		if (trim($precio)=='' || trim($titulo)=='' || !is_numeric($precio)) {
			$resultado.=" DATOS OBLIGATORIOS";
		} else {
			$id=uniqid();
			//validar si existe el libro
			if (existeLibro($titulo,$id)) {
				echo "libro ya existe";
			} else {
					$libreria[$id] = array('titulo'=>$titulo, 'precio'=>$precio);
					$error='<br>id:'.$id.' alta libro efectuada';
					//print_r($libreria);
			}		
		}
	}

	//modificacion del libro
		if (isset($_POST['accion'])) {
			echo "entro en accion";
			$id=$_POST['id'];
			$titulo=$_POST['titulo']; 
			$precio=$_POST['precio']; 
			if (trim($titulo)=='') {
				$resultado.="-Titulo no informado";
			}
			if (trim($precio)=='' || !is_numeric($precio)) {
				$resultado.=" -Precio no informado o no es un numérico";
			}
			if (trim($precio)=='' || trim($titulo)=='' || !is_numeric($precio)) {
				$resultado.=" DATOS OBLIGATORIOS";
			} else {
				//validar si existe el libro
				if (existeLibro($titulo,$id)) {
					echo "libro ya existe";
				} else {
					$libreria[$id] = array('titulo'=>$titulo, 'precio'=>$precio);
					$error='<br>id:'.$id.' modificacion efectuada';
					print_r($libreria);
				}		
			}
		}	
	
	//borrar libro  - borrar registro - borrar elemento de la sesión 
	if (isset($_POST['bajalibro'])) {
		echo "entro en bajalibro";
		$indice=$_POST['id'];
		unset($libreria[$indice]);
	}

	//muestra la libreria
	if (isset($libreria)){
		//echo "entro en libros";
		$libros=null;
		//ordenar el array por nif
			//ksort($libreria);
		//ordenar por nombre
			//asort($libreria);
		//creamos un array con las claves del array de libreria (el id)
		$claves = array_keys($libreria);
		//creamos un array con el dato (columna) que queremos ordenar (la precio)
		$precios = array_column($libreria, 'precio');
		//ordenamos el array de precios de forma ascendente y, simultaneamente, se ordenara el array de libreria y el de claves por la misma ordenación de claves que el primero
		array_multisort($precios, SORT_ASC, $libreria, $claves);
		//substituimos las claves del array de libreria por las del array de claves 
		$libreria = array_combine($claves, $libreria);

		//recorrer la variable de sesión con un bucle para construir las etiquetas tr de la tabla
		foreach ($libreria as $id => $datosLibreria) {
			$libros.="<tr>";
			$libros.="<td class='id' disableb >$id</td>";
			$libros.="<td><input type='text' value='$datosLibreria[titulo]' class='titulo' /> </td>";
			$libros.="<td><input type='number' value='$datosLibreria[precio]' class='precio' /> </td>";

			$libros.="<td>";
					$libros.="<form method='post' action='#'>";
						$libros.="<input type='hidden' name='id' value='$id'>";
						$libros.="<input type='submit' name='bajalibro' value='baja'>";
					$libros.="</form>";
					$libros.="<input type='button' value='Modificar' class='modificar'>";
				$libros.="</td>";
			$libros.="</tr>";
		}
	}
	
	// function existeLibro($titulo) {
	// 	global $libreria;
	// 	//crear un array que contenga los titulos de todos los libros
	// 	$titulos=array_column($libreria,'titulo');
	// 	print_r($titulos);
	// 	return in_array($titulo, $titulos); 
		
	// }
	function existeLibro($titulo,$id) {
		//buscar el array libreria fuera de la función
		global $libreria;

		foreach ($libreria as $clave => $valor) {
			if (in_array($titulo, $valor) && $id != $clave) {
				return true;
			}
		}

		return false;
	}

	//actualizar la variable de sesion
	$_SESSION['libros']=$libreria;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>libros - con clasificacion</title>
	<style type="text/css">
		div.container {
			margin: auto; width:920px; text-align: center;
		}
		table {
			border: 5px ridge blue;
			width: 800px;
		}
		th, td {
			background:white; width:auto; border: 2px solid green; text-align: left;
		}
		input[type=text] {width: 330px;}
	</style>
	<script type="text/javascript" src='https://code.jquery.com/jquery-3.1.1.min.js'></script>
	<script type="text/javascript">
		window.onload = function() {
			//recuperar botones a recuperar
			var botones=document.querySelectorAll('.modificar');
			//activar listener para modificar (tipo de boton y función a lanzar)
			for (i=0; i< botones.length; i++) {
				botones[i].addEventListener('click', modificar);
			}
		}

		function modificar() {
			alert ("modificar desde javascript");
			//situarnos en la etiqueta TR de la fila sobre la que hemos pulsado el boton de modificar
			// opcion 1 >>>>> var tr=this.parentNode.parentNode;
			//opcion 2 >>>>>> closest busca la etiqueta más cercana del tipo que se indique
			var tr=this.closest('tr');
			//recuperar los datos a partir de la etiqueta TR
			var id=tr.querySelector('.id').innerText;
			var titulo=tr.querySelector('.titulo').value;
			var precio=tr.querySelector('.precio').value;
			//informar el formulario oculto
			document.getElementById('id').value=id;
			document.getElementById('titulo').value=titulo;
			document.getElementById('precio').value=precio;
			// //enviar formulario al servidor
			document.getElementById('formulario').submit();
		}	
		
	</script>
</head>
<body>
	<div class="container">
		<h2 style="text-align:center">EJERCICIO LIBRERIA</h2>
		<span><?=$resultado?></span><br><br>
		<form name="formularioalta" method="post" action="#">
			<table border='2'>
				<tr><th>Título</th>
					<th>Precio</th>
					<th colspan='2' style='width:150px'>Opción</th>
				</tr>
				<tr>
					<td><input type='text' size='50' maxlenght='100' name='titulo' /></td>
					<td><input type='number' maxlenght='5' name='precio' min="1"/></td>
					<td colspan='2'><input type='submit' name='alta' value='Agregar' /></td>
				</tr>
			</table>
		</form><br>
		<form name="formulario" id="formulario" method="post" action="#"> 
			<!--estos input hidden sirven para guardar la id del libro a modificar o dar de baja-->
			<input type="hidden" name="id" id="id">
			<input type="hidden" name="titulo" id="titulo">
			<input type="hidden" name="precio" id="precio">
			<input type="hidden" name="accion" id="accion">
		</form>
		<div>
			<table>
				<?php echo $libros; ?>
			</table>
		</div>
	</div>
</body>
</html>