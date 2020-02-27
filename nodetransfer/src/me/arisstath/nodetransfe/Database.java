package me.arisstath.nodetransfe;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.Statement;
import java.util.ArrayList;


public class Database {

	private Connection conn;
	private String host, username, password, database;

	// deathbot coded most of this shit xd basically everything because the old
	// version was SHITTY
	public Database(String host, String username, String password, String database) {

		this.host = host;
		this.username = username;
		this.password = password;
		this.database = database;
		connect();
		
	}
	private void connect(){
		String database = "jdbc:mysql://" + host + ":3306/" + this.database;
		while(true){
			try {
				Class.forName("com.mysql.jdbc.Driver");
				conn = DriverManager.getConnection(database, username, password);
				System.out.println("[INFO] Connection successed.");
				break;
			} catch (Exception ex) {
				System.err.println("Failed to open a connection with the database: " + ex.getMessage());
				// System.exit(-1);
			}
		}
	}
	public Statement createStatement(){
		Statement st;
		while(true){
			try {
				st = conn.createStatement();
				break;
			} catch (Exception e) {
				connect();
			}
		}
		return st;
	}
	
	private PreparedStatement createStatement(String sql, int closeCurrentResult){
		PreparedStatement st;
		while(true){
			try {
				st = conn.prepareStatement(sql, closeCurrentResult);
				break;
			} catch (Exception e) {
				connect();
			}
		}
		return st;
	}
	public String getUsername(int id) throws Exception{
		PreparedStatement statement = createStatement("SELECT * FROM mcservers WHERE id=?", 0);
		statement.setInt(1, id);
		ResultSet set = statement.executeQuery();
		if(!set.next()){
			return null;
		}
		return set.getString("username");
	}
	public ArrayList<TransferJob> getPendingTransfers() throws Exception{
		ArrayList<TransferJob> toreturn = new ArrayList<>();
		PreparedStatement statement = createStatement("SELECT * FROM transfer WHERE fromnode=? AND status=?", 0);
		statement.setString(1, Main.node);
		statement.setString(2, "QUEUED");
		ResultSet set = statement.executeQuery();
		while(set.next()){
			String targetIP = Main.callURL("http://89.36.217.73/ipresolver.php?server=" + set.getString("tonode"));
			if (targetIP.isEmpty()) {
				System.out.println("[-] Could not resolve the IP of the target node.");
				updateJob(set.getInt("id"), "INVALID_IP");
				continue;
			}
			if (!validateOwnership(set.getInt("serverid"))) {
				System.out.println("[-] The specified server(" + set.getInt("serverid") + ") is not here.");
				updateJob(set.getInt("id"), "INVALID_NODE");
				continue;
			}
			updateJob(set.getInt("id"), "AQUIRED");
			toreturn.add(new TransferJob(set.getInt("id"), targetIP, set.getString("tonode"), set.getInt("serverid")));
		}
		return toreturn;
	}
	public void updateJob(int id, String newStatus) throws Exception{
		PreparedStatement statement = createStatement("UPDATE transfer SET status=? WHERE id=?", 0);
		statement.setString(1, newStatus);
		statement.setInt(2, id);
		statement.execute();
	}
	public ArrayList<MCServer> getAllServers() throws Exception{
		ArrayList<MCServer> toreturn = new ArrayList<>();
		PreparedStatement statement = createStatement("SELECT * FROM mcservers WHERE node=?", 0);
		statement.setString(1, Main.node);
		ResultSet set = statement.executeQuery();
		while(set.next()){
			toreturn.add(new MCServer("/home/mc_" + set.getInt("id"), set.getInt("id")));
		}
		return toreturn;
	}
	public String getCurrentDaemon(int id) throws Exception{
		PreparedStatement statement = createStatement("SELECT * FROM mcservers WHERE id=?", 0);
		statement.setInt(1, id);
		ResultSet set = statement.executeQuery();
		if(!set.next()){
			return null;
		}
		return set.getString("node");
	}
	public String getDaemonToken(int id) throws Exception{
		PreparedStatement statement = createStatement("SELECT * FROM users WHERE username=?", 0);
		statement.setString(1, "test");
		ResultSet set = statement.executeQuery();
		if(!set.next()){
			return null;
		}
		return set.getString("token");
	}
	public String getDirectory(int id) throws Exception{
		PreparedStatement statement = createStatement("SELECT * FROM mcservers WHERE id=?", 0);
		statement.setInt(1, id);
		ResultSet set = statement.executeQuery();
		if(!set.next()){
			return null;
		}
		return "/home/mc_" + set.getInt("id");
	}
	public boolean validateOwnership(int id) throws Exception{
		PreparedStatement statement = createStatement("SELECT * FROM mcservers WHERE id=?", 0);
		statement.setInt(1, id);
		ResultSet set = statement.executeQuery();
		if(!set.next()){
			return false;
		}
		return set.getString("node").equalsIgnoreCase(Main.node);
	}
	public void updateServer(int id, String newNode, String newIP) throws Exception{
		PreparedStatement statement = createStatement("UPDATE mcservers SET node=?,ip=? WHERE id=?", 0);
		statement.setString(1, newNode);
		statement.setString(2, newIP);
		statement.setInt(3, id);
		statement.execute();
	}

}
