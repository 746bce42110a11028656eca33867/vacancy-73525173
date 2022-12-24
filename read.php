<?
	require('db.php');
	require('data_processing_unit.php');
	$DPU = new data_processing_unit();
	// just request all DB content. no checks, cursors, etc...
	$data = $DPU->unfold_crud_r( $pdo->query( $DPU->prepare_crud_r( ) )->fetchAll(PDO::FETCH_UNIQUE) );
	header("Content-Type: application/json; charset=utf-8", true);
	echo json_encode( $data );