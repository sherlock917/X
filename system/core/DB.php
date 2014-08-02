<?php
require_once SYSTEM_PATH . '/util/DatabaseUtil.php';
/**
 * 数据库访问对象(对pdo的封装)
 *
 * @author vincent
 *        
 */
class DB {
	private static $_instance; // 数据库访问对象DB实例
	protected $pdo;
	/**
	 * 构造函数 (不能直接创建db对象)
	 */
	private function __construct() {
		$db_config = $GLOBALS ['db_config'];
		$dsn = sprintf ( "%s:dbname=%s;host=%s", $db_config ['database'], $db_config ['dbname'], $db_config ['host'] );
		$this->pdo = new PDO ( $dsn, $db_config ['user_name'], $db_config ['password'] );
	}
	
	/**
	 * 析构函数
	 */
	function __destruct() {
	}
	/**
	 * 获取pdo引用
	 */
	function getPdo() {
		return $this->pdo;
	}
	
	/**
	 * 获取数据库访问对象实例
	 */
	static function getInstance() {
		// 一次请求只能有1个DB对象
		if (! isset ( self::$_instance )) {
			self::$_instance = new DB ();
		}
		return self::$_instance;
	}
	/**
	 * 创建数据表
	 *
	 * @param string $modelName
	 */
	static function createTable($modelName) {
		$modelPath = APPLICATION_PATH . '/models/' . $modelName . '.php';
		if (! file_exists ( $modelPath ))
			throw new Exception ( 'model ' . $modelName . ' is not exist' );
		include_once APPLICATION_PATH . '/models/' . $modelName . '.php';
		$db = self::getInstance ();
		return $db->exec ( DatabaseUtil::getCreateTableQuery ( $modelName ) );
	}
	/**
	 * 删除数据表
	 *
	 * @param string $modelName
	 * @return Ambigous <PDOStatement, number>
	 */
	static function dropTable($modelName) {
		$db = self::getInstance ();
		return $db->exec ( DatabaseUtil::getDropTableQuery ( $modelName ) );
	}
	
	/**
	 * 插入数据
	 *
	 * @param string $tableName
	 * @param array $arr
	 * @return number
	 */
	function insert($tableName, $arr) {
		$stat = $this->pdo->prepare ( DatabaseUtil::getInsertQuery ( $tableName, array_keys ( $arr ) ) );
		return $stat->execute ( array_values ( $arr ) );
	}
	
	/**
	 * 选择数据
	 *
	 * @param string $tableName
	 * @param array $arr
	 * @return multitype:
	 */
	function select($tableName, $arr = array()) {
		$stat = $this->pdo->prepare ( DatabaseUtil::getSelectQuery ( $tableName, array_keys ( $arr ) ) );
		$stat->execute ( array_values ( $arr ) );
		return $stat->fetchAll ();
	}
	/**
	 * 删除数据
	 *
	 * @param string $tableName
	 * @param array $arr
	 * @return PDOStatement
	 */
	function delete($tableName, $arr) {
		$stat = $this->pdo->prepare ( DatabaseUtil::getDeleteQuery ( $tableName, array_keys ( $arr ) ) );
		return $stat->execute ( array_values ( $arr ) );
	}
	/**
	 * 更新数据
	 * data1为更新后的数据 data2用于选择要更新的数据
	 *
	 * @param string $tableName
	 * @param array $newData
	 * @param array $conditions
	 */
	function update($tableName, $newData, $conditions) {
		$stat = $this->pdo->prepare ( DatabaseUtil::getUpdateQuery ( $tableName, array_keys ( $newData ), array_keys ( $conditions ) ) );
		return $stat->execute ( array_merge ( array_values ( $newData ), array_values ( $conditions ) ) );
	}
	
	/**
	 * 执行sql语句
	 *
	 * @param string $statement
	 * @return PDOStatement
	 */
	function query($sql) {
		return $this->pdo->query ( $sql );
	}
	/**
	 * 执行sql语句
	 *
	 * @param string $statement
	 * @return PDOStatement
	 */
	function exec($sql) {
		return $this->pdo->exec ( $sql );
	}
	
	/**
	 * 开始事务
	 */
	function beginTransaction() {
		$this->pdo->beginTransaction ();
	}
	
	/**
	 * 提交事务
	 */
	function commit() {
		$this->pdo->commit ();
	}
	/**
	 * 回滚
	 */
	function rollBack() {
		$this->pdo->rollBack ();
	}
	/**
	 * 设置是否自动提交
	 *
	 * @param boolean $value
	 */
	function setAutoCommit($value = TRUE) {
		$this->pdo->setAttribute ( PDO::ATTR_AUTOCOMMIT, $value );
	}
}
 