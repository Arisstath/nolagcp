package me.arisstath.nolagbot;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.Statement;

import net.dv8tion.jda.core.entities.User;

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
	public void updateDiscord(String username, User user) {
		try {
			PreparedStatement statement = createStatement("UPDATE users SET discordid=? WHERE username=?", 0);
			statement.setString(1, user.getId());
			statement.setString(2, username);
			statement.execute();
		} catch (Exception e) {
		}
	}
	public String getNoLagServer(String username, int id) {
		System.out.println("Username: " + username);
		System.out.println("Id: " + id);
		try {
			PreparedStatement statement = createStatement("SELECT * FROM mcservers WHERE username=? AND id=?", 0);
			statement.setString(1, username);
			statement.setInt(2, id);
			ResultSet set = statement.executeQuery();
			if (!set.next()) {
				System.out.println("doesnt have next");
				return null;
			}
			return set.getString("ip") + ":" + set.getInt("port");
		} catch (Exception e) {
			e.printStackTrace();
			return null;
		}
	}
	public String fetchUsernameFromPin(String supportPIN) {
		try {
			PreparedStatement statement = createStatement("SELECT * FROM users WHERE spin=?", 0);
			statement.setString(1, supportPIN);
			ResultSet set = statement.executeQuery();
			if (!set.next()) {
				return null;
			}
			return set.getString("username");
		} catch (Exception e) {
			return null;
		}
	}

	public String getLinkedDiscord(String supportPIN) {
		try {
			PreparedStatement statement = createStatement("SELECT * FROM users WHERE spin=?", 0);
			statement.setString(1, supportPIN);
			ResultSet set = statement.executeQuery();
			if (!set.next()) {
				return null;
			}
			return set.getString("discordid");
		} catch (Exception e) {
			return null;
		}
	}

}
