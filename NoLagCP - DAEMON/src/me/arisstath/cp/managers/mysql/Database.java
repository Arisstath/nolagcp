package me.arisstath.cp.managers.mysql;

import java.beans.PropertyVetoException;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;

import org.apache.ftpserver.ftplet.Authority;
import org.apache.ftpserver.usermanager.impl.BaseUser;
import org.apache.ftpserver.usermanager.impl.ConcurrentLoginPermission;
import org.apache.ftpserver.usermanager.impl.WritePermission;

import com.mchange.v2.c3p0.ComboPooledDataSource;

import me.arisstath.cp.Main;
import me.arisstath.cp.obj.srv.MinecraftServer;
import me.arisstath.cp.obj.srv.Service;
import me.arisstath.cp.obj.usr.NoLagCustomer;

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

	public int getTotalRamUse() {
		try {
			ResultSet set = createStatement()
					.executeQuery("SELECT * FROM mcservers WHERE node='" + me.arisstath.cp.Main.getNode() + "'");
			// hmm clean server woo
			if (!set.next()) {
				return 0;
			}
			
			
			int totalram = 0;
			totalram += set.getInt("ram"); // since we already called next
			while (set.next()){
				int sstatus = getService(set.getInt("serviceid")).getPaid();
				if(sstatus == 3 || sstatus == 4){
					continue;
				}
				totalram += set.getInt("ram");
			}
			set.close();
			return totalram;
		} catch (Exception ex) {
			// ex.printStackTrace();
			return 1000000; // don't choose this node, it's fucked up and doesnt
							// work
		}
	}

	private void connect() {
		ComboPooledDataSource cpds = new ComboPooledDataSource();
		try {
			cpds.setDriverClass( "com.mysql.jdbc.Driver" );
		} catch (PropertyVetoException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} 
		
		cpds.setJdbcUrl("jdbc:mysql://" + host + ":3306/" + this.database);
		cpds.setUser(username);
		cpds.setPassword(password);
		if(Main.getNode().toLowerCase().startsWith("us")){
			cpds.setMinPoolSize(20);
			cpds.setAcquireIncrement(10);
			cpds.setMaxPoolSize(100);
		} else {
			cpds.setMinPoolSize(10);
			cpds.setAcquireIncrement(5);
			cpds.setMaxPoolSize(50);
		}
		
		try {
			conn = cpds.getConnection();
		} catch (SQLException e) {
			e.printStackTrace();
			System.exit(-1);
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

	public ArrayList<MinecraftServer> getServers() {
		try {
			PreparedStatement st = createStatement("SELECT * FROM mcservers WHERE node=?", 0);
			st.setString(1, Main.getNode());
			ResultSet set = st.executeQuery();
			ArrayList<MinecraftServer> servers = new ArrayList<>();
			while (set.next()) {
				String idd = set.getString("id");
				String name = set.getString("name");
				String port = set.getString("port");
				String ip = set.getString("ip");
				int serviceid = set.getInt("serviceid");
				//String node = set.getString("node");
				String ram = set.getString("ram");
				String username = set.getString("username");
				String ftpuser = set.getString("ftpusername");
				String ftppass = set.getString("ftppass");
				boolean autosave = set.getString("autosave").equals("true");
				boolean autorestart = set.getString("autorestart").equals("true");
				MinecraftServer srv = new MinecraftServer("/home/mc_" + username + "/" + idd + "/", idd, name, username,
						ram, ip, port, "server.jar", autosave, autorestart);
				srv.setServiceid(serviceid);
				srv.setFtpusername(ftpuser);
				srv.setFtppass(ftppass);
				servers.add(srv);
			}
			set.close();
			st.close();
			return servers;
		} catch (Exception ex) {
			return null;
		}
	}

	public MinecraftServer getServerById(String id) {
		try {
			PreparedStatement st = createStatement("SELECT * FROM mcservers WHERE id=?", 0);
			st.setString(1, id);
			ResultSet set = st.executeQuery();
			if (!set.next()) {
				return null;
			}
			String idd = set.getString("id");
			String name = set.getString("name");
			String port = set.getString("port");
			String ip = set.getString("ip");
			int serviceid = set.getInt("serviceid");
			//String node = set.getString("node");
			String ram = set.getString("ram");
			String username = set.getString("username");
			String ftpuser = set.getString("ftpusername");
			String ftppass = set.getString("ftppass");
			boolean autosave = set.getString("autosave").equals("true");
			boolean autorestart = set.getString("autorestart").equals("true");
			MinecraftServer srv = new MinecraftServer("/home/" + username + "/" + idd + "/", idd, name, username, ram,
					ip, port, "server.jar", autosave, autorestart);
			srv.setServiceid(serviceid);
			srv.setFtpusername(ftpuser);
			srv.setFtppass(ftppass);
			set.close();
			st.close();
			return srv;
		} catch (Exception ex) {
			return null;
		}
	}

	public boolean ownsServer(String username, String id) {
		try {
			//too many queries here...
			PreparedStatement stUser = createStatement("SELECT * FROM users WHERE username=?", 0);
			stUser.setString(1, username);
			ResultSet sett = stUser.executeQuery();
			if (sett.next()) {
				// System.out.println("next");
				
				if (sett.getInt("rank") >= 2) {	
					sett.close();
					stUser.close();
					return true;
				}
			}
			sett.close();
			stUser.close();
			PreparedStatement st = createStatement("SELECT * FROM mcservers WHERE id=? AND username=?", 0);
			// System.out.println("passed id = " + id);
			st.setInt(1, Integer.parseInt(id));
			st.setString(2, username);
			ResultSet set = st.executeQuery();
			if (set.next()) {
				return true;
			}
			// check for subuser
			st = createStatement("SELECT * FROM subusers WHERE serverid=? AND username=?", 0);
			// System.out.println("passed id = " + id);
			st.setInt(1, Integer.parseInt(id));
			st.setString(2, username);
			set = st.executeQuery();
			if (set.next()) {
				return true;
			}
			return false;
		} catch (Exception ex) {
			ex.printStackTrace();
			return false;
		}
	}

	public boolean doesExist(String username) {
		try {

			List<Authority> authorities = new ArrayList<Authority>();
			authorities.add(new WritePermission());
			PreparedStatement s = createStatement("SELECT * from mcservers WHERE node=? AND ftpusername=?", 0);
			s.setString(1, Main.getNode());
			s.setString(2, username);
			ResultSet set = s.executeQuery();
			return set.next();
		} catch (Exception ex) {
			ex.printStackTrace();
		}
		return false;
	}

	public String[] getFTPUsers() {
		List<String> users = new ArrayList<String>();
		try {

			List<Authority> authorities = new ArrayList<Authority>();
			authorities.add(new WritePermission());
			// Main.log("Loading all customers in this node...", LogType.INFO);
			PreparedStatement s = createStatement("SELECT * from mcservers WHERE node=?", 0);
			s.setString(1, Main.getNode());
			ResultSet set = s.executeQuery();
			while (set.next()) {
				users.add(set.getString("ftpusername"));
			}
			set.close();
			s.close();
			return users.toArray(new String[0]);
		} catch (Exception ex) {
			ex.printStackTrace();
		}
		return null;
	}

	public BaseUser getFTPUser(String username) {
		try {
			List<Authority> authorities = new ArrayList<Authority>();
			authorities.add(new WritePermission());
			authorities.add(new ConcurrentLoginPermission(900, 900));
			
			// Main.log("Loading all customers in this node...", LogType.INFO);
			PreparedStatement s = createStatement("SELECT * from mcservers WHERE node=? AND ftpusername=?", 0);
			s.setString(1, Main.getNode());
			s.setString(2, username);
			ResultSet set = s.executeQuery();
			while (set.next()) {
				// System.out.println(set.getString("username"));
				// NoLagCustomer customer =
				// getCustomerByUsername(set.getString("username"));
				// Main.log("Discovered new customer '" + customer.getUsername()
				// + "'", LogType.INFO);
				BaseUser user = new BaseUser();
				user.setName(set.getString("ftpusername"));
				user.setPassword(set.getString("ftppass"));
				user.setHomeDirectory("/home/mc_" + set.getString("id") + "/");
				user.setAuthorities(authorities);
				return user;
			}
			set.close();
			s.close();

		} catch (Exception ex) {
			ex.printStackTrace();
		}
		return null;
	}

	public MinecraftServer getServerByFTPUser(String id) {
		try {
			PreparedStatement st = createStatement("SELECT * FROM mcservers WHERE ftpusername=?", 0);
			st.setString(1, id);
			ResultSet set = st.executeQuery();
			if (!set.next()) {
				return null;
			}
			String idd = set.getString("id");
			String name = set.getString("name");
			String port = set.getString("port");
			String ip = set.getString("ip");
			int serviceid = set.getInt("serviceid");
			//String node = set.getString("node");
			String ram = set.getString("ram");
			String username = set.getString("username");
			String ftpuser = set.getString("ftpusername");
			String ftppass = set.getString("ftppass");
			boolean autosave = set.getString("autosave").equals("true");
			boolean autorestart = set.getString("autorestart").equals("true");
			MinecraftServer srv = new MinecraftServer("/home/" + username + "/" + idd + "/", idd, name, username, ram,
					ip, port, "server.jar", autosave, autorestart);
			srv.setServiceid(serviceid);
			srv.setFtpusername(ftpuser);
			srv.setFtppass(ftppass);
			set.close();
			st.close();
			return srv;
		} catch (Exception ex) {
			return null;
		}
	}

	public NoLagCustomer getCustomerByToken(String token) {
		try {
			PreparedStatement st = createStatement("SELECT * from users WHERE token=?", 0);
			st.setString(1, token);
			ResultSet set = st.executeQuery();
			if (!set.next()) {
				return null;
			}
			String username = set.getString("username");
			String email = set.getString("email");
			NoLagCustomer obj = new NoLagCustomer();
			obj.setEmail(email);
			obj.setUsername(username);
			obj.setToken(token);
			set.close();
			st.close();
			// passwords are now per server, needed for more security & subusers
			// logic
			// obj.setFtppass(set.getString("ftppass"));
			return obj;
		} catch (Exception ex) {
			ex.printStackTrace();
			return null;
		}
	}

	public void setStatus(Service service, int status) {
		try {
			PreparedStatement st = createStatement("UPDATE services SET active=? WHERE id=?", 0);
			st.setInt(1, status);
			st.setInt(2, service.getId());
			st.executeUpdate();
			st.close();
		} catch (Exception e) {
			e.printStackTrace();
			// TODO: handle exception
		}
	}

	public Service getService(int id) {
		try {
			PreparedStatement st = createStatement("SELECT * from services WHERE id=?", 0);
			st.setInt(1, id);
			ResultSet set = st.executeQuery();
			if (!set.next()) {
				return null;
			}
			Service obj = new Service();
			obj.setId(set.getInt("id"));
			obj.setPaid(set.getInt("active"));
			set.close();
			st.close();
			return obj;
		} catch (Exception ex) {
			return null;
		}
	}

	public NoLagCustomer getCustomerByUsername(String username) {
		try {
			PreparedStatement st = createStatement("SELECT * from users WHERE username=?", 0);
			st.setString(1, username);
			ResultSet set = st.executeQuery();
			if (!set.next()) {
				return null;
			}
			// String username = set.getString("username");
			String email = set.getString("email");
			String token = set.getString("token");
			NoLagCustomer obj = new NoLagCustomer();
			obj.setEmail(email);
			obj.setUsername(username);
			obj.setToken(token);
			set.close();
			st.close();
			// obj.setFtppass(set.getString("ftppass"));
			return obj;
		} catch (Exception ex) {
			return null;
		}
	}


	private PreparedStatement createStatement(String sql, int closeCurrentResult) {
		PreparedStatement st;
		while (true) {
			try {
				st = conn.prepareStatement(sql, closeCurrentResult);
				break;
			} catch (Exception e) {
				connect();
				try {
					Thread.sleep(1000); //try every sec
				} catch (InterruptedException e1) {
					// TODO Auto-generated catch block
					e1.printStackTrace();
				} 
			}
		}
		return st;
	}

}
