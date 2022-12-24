<?
$dsn = "sqlite:db.sqlite";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn,  '', '', array(
	   \PDO::ATTR_EMULATE_PREPARES => false,
	   \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
	   \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
	));
	$pdo->query(
		"
		CREATE TABLE  IF NOT EXISTS measures (
		    [key]      STRING (20, 20) PRIMARY KEY
		                               UNIQUE
		                               NOT NULL,
		    date       STRING          NOT NULL,
		    time_start STRING          NOT NULL,
		    time_end   TEXT            NOT NULL,
		    div        STRING (3, 3)   NOT NULL,
		    vals       STRING          NOT NULL,
		    id         STRING          NOT NULL
		);
		"
	);

} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

