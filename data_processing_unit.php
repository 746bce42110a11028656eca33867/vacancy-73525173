<?php
class data_processing_unit {
	const valves = [
					[ "cell_1_1","cell_1_2","cell_1_3","cell_1_4","cell_1_5","cell_1_6","cell_1_7","cell_1_8" ],
					[ "cell_2_1","cell_2_2","cell_2_3","cell_2_4","cell_2_5","cell_2_6","cell_2_7","cell_2_8" ],
					[ "cell_3_1","cell_3_2","cell_3_3","cell_3_4","cell_3_5","cell_3_6","cell_3_7","cell_3_8" ],
					[ "cell_4_1","cell_4_2","cell_4_3","cell_4_4","cell_4_5","cell_4_6","cell_4_7","cell_4_8" ],
					[ "cell_5_1","cell_5_2","cell_5_3","cell_5_4","cell_5_5","cell_5_6","cell_5_7","cell_5_8" ],
					[ "cell_6_1","cell_6_2","cell_6_3","cell_6_4","cell_6_5","cell_6_6","cell_6_7","cell_6_8" ]
				];
	const table_name = "measures";
	private function compose_datatable_primary_key( array $data ) {
		return $data['date'].'-'.$data['time_start'].'-'.$data['time_end'].'-'.$data['div'];
	}
	public function is_data_valid( array $data ) {
		/* 
			valid data are:
			1. have valid date
			2. have time start
			3. have time end
			4. time start < time end
			5. have data cells keys
			6. have non empty id key
		*/
		// note about the date field: we can get mm/dd/yyyy or mm/dd/yyyy-... formats.
		// ommit everything from and so on `-`
		if( !@$data['date'] || !( list($mm,$dd,$yyyy) = explode( '/', explode( '-', $data['date'] )[0] ) ) || !checkdate( $mm, $dd, $yyyy ) ) return false;
		if( !@$data['time_start'] ) return false;
		if( !@$data['time_end'] ) return false;
		if( $data['time_start'] >= $data['time_end'] ) return false;
		foreach( call_user_func_array( 'array_merge', self::valves ) as $essential_key ) {
			if( !array_key_exists($essential_key, $data) ) return false;
		}
		if( !@$data['id'] || strlen( $data['id'] ) < 1 ) return false;
		return true;
	}
	public function prepare_crud_c( array $data ) {
		$primary_key = self::compose_datatable_primary_key( $data );
		$combined_data = [];
		foreach( call_user_func_array( 'array_merge', self::valves ) as $essential_key ) {
			$combined_data[ $essential_key ] = $data[ $essential_key ];
		}
		$combined_data = serialize($combined_data);
		$sql = "INSERT INTO ".self::table_name." ( [key], date, time_start, time_end, div, vals, id ) VALUES ( ?, ?, ?, ?, ?, ?, ? )";
		return [ $sql, [ $primary_key, $data['date'], $data['time_start'], $data['time_end'], $data['div'], $combined_data, $data['id'] ] ];
	}
	public function prepare_crud_r() {
		return "SELECT * FROM ".self::table_name;
	}
	public function unfold_crud_r( array $data ) {
		foreach( $data as $k=>$v ) {
			$data[ $k ][ 'vals' ] = unserialize( $data[ $k ][ 'vals' ] );
		}
		return $data;
	}
	public function prepare_crud_u( array $data ) {
		$primary_key = $data['date'];
		$combined_data = [];
		foreach( call_user_func_array( 'array_merge', self::valves ) as $essential_key ) {
			$combined_data[ $essential_key ] = $data[ $essential_key ];
		}
		$combined_data = serialize($combined_data);
		$sql = "UPDATE ".self::table_name." SET time_start = ?, time_end = ?, div = ?, vals = ?, id = ? WHERE [key] = ?";
		return [ $sql, [ $data['time_start'], $data['time_end'], $data['div'], $combined_data, $data['id'], $primary_key] ];
	}
	public function is_data_can_be_deleted( string|array $data ) {
		// yes we can
		return true;
	}
	public function prepare_crud_d( string|array $data ) {
		if( is_array( $data ) ) $data = $data[0];
		$sql = "DELETE FROM ".self::table_name." WHERE [key] = ?;";
		return [ $sql, [$data] ];
	}
}