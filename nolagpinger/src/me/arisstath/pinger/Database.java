package me.arisstath.pinger;

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

	private void connect() {
		String database = "jdbc:mysql://" + host + ":3306/" + this.database;
		while (true) {
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

	public Statement createStatement() {
		Statement st;
		while (true) {
			try {
				st = conn.createStatement();
				break;
			} catch (Exception e) {
				connect();
			}
		}
		return st;
	}

	private PreparedStatement createStatement(String sql, int closeCurrentResult) {
		PreparedStatement st;
		while (true) {
			try {
				st = conn.prepareStatement(sql, closeCurrentResult);
				break;
			} catch (Exception e) {
				connect();
			}
		}
		return st;
	}

	public ArrayList<String> getIPS() {
		ArrayList<String> ips = new ArrayList<>();
		try {
			ResultSet set = createStatement("SELECT * FROM mcservers", 0).executeQuery();
			while (set.next()) {
				ResultSet sett = createStatement("SELECT * FROM services WHERE id=" + set.getInt("serviceid"), 0).executeQuery();
				if(sett.next()){
					if(sett.getInt("active") != 1 && sett.getInt("active") != 2){
						continue;
					}
				} else {
					continue;
				}
				sett.close();
				//We have info here
				System.out.println("new ip");
				ips.add(set.getString("ip") + ":" + set.getString("port"));
			}
		} catch (Exception ex) {
			ex.printStackTrace();
		}
		System.out.println("total ips " + ips.size());
		return ips;
	}

}
