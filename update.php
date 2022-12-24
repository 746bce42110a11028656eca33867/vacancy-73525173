<?
	http_response_code( 400 );
	require('db.php');
	require('data_processing_unit.php');
try {
	$content_raw = file_get_contents("php://input");
	$decoded_data = json_decode($content_raw, true);
	$DPU = new data_processing_unit();
	if( $DPU->is_data_valid( $decoded_data ) ) {
		list( $sql, $data ) = $DPU->prepare_crud_u( $decoded_data );
		try {
		    $pdo->beginTransaction();
		    $stmt = $pdo->prepare( $sql );
		    $exec_code = $stmt->execute( $data );
		    $pdo->commit();
		    print_r( [ $sql, $data, '$exec_code'=>$exec_code, '$stmt->errorCode()'=>$stmt->errorCode() ] );
		    http_response_code( 200 );
		} catch (Exception $e) {
		    $pdo->rollback();
		    throw $e;
		}
	} else {
		throw new Exception ( "Got malformed data for save" );
	}
} catch (Exception $e) {
     throw new Exception ($e->getMessage(), (int)$e->getCode());
}
