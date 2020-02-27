package me.arisstath.cp.managers.ftp;

import java.io.File;
import java.io.PrintWriter;
import java.util.HashMap;
import java.util.Map;

import org.apache.ftpserver.ConnectionConfigFactory;
import org.apache.ftpserver.FtpServer;
import org.apache.ftpserver.FtpServerFactory;
import org.apache.ftpserver.ftplet.FtpException;
import org.apache.ftpserver.ftplet.Ftplet;
import org.apache.ftpserver.ftplet.UserManager;
import org.apache.ftpserver.listener.ListenerFactory;
import org.apache.ftpserver.usermanager.PasswordEncryptor;
import org.apache.ftpserver.usermanager.PropertiesUserManagerFactory;

import me.arisstath.cp.Main;
import me.arisstath.cp.Main.LogType;
import me.arisstath.cp.utils.EncryptionUtils;

public class FTPManager {

	public static String aesKey = "";
	public UserManager um;
	public PropertiesUserManagerFactory userManagerFactory;
	public FtpServerFactory serverFactory;
	public static FtpServer server;

	public void start() {
		Main.log("Starting FTP Manager...", LogType.INFO);
		serverFactory = new FtpServerFactory();
		ListenerFactory factory = new ListenerFactory();
		factory.setPort(1234);// set the port of the listener (choose your
								// desired port, not 1234)
		serverFactory.addListener("default", factory.createListener());
		userManagerFactory = new PropertiesUserManagerFactory();

		// empty users file

		PrintWriter writer;
		try {
			writer = new PrintWriter(new File("user.properties"));
			writer.println("");
			writer.close();
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		userManagerFactory.setFile(new File("user.properties"));// choose any.
																// We're telling
																// the
																// FTP-server
																// where to read
																// it's user
																// list
		userManagerFactory.setPasswordEncryptor(new PasswordEncryptor() {

			@Override
			public String encrypt(String password) {
				return password; // password is passed crypted already, so no
									// need to encrypt
			}

			@Override
			public boolean matches(String passwordToCheck, String storedPassword) {
				return passwordToCheck.equals(EncryptionUtils.decrypt(storedPassword, "u>M&3gPCUMnc['7S"));
			}
		});
		// Let's add a user, since our myusers.properties files is empty on our
		// first test run
		/*
		 * BaseUser user = new BaseUser(); user.setName("arisstath");
		 * user.setPassword("test");
		 * user.setHomeDirectory("/home/XenoPanel/Arisstathfasf");
		 * List<Authority> authorities = new ArrayList<Authority>();
		 * authorities.add(new WritePermission());
		 * user.setAuthorities(authorities); um =
		 * userManagerFactory.createUserManager(); List<BaseUser> ftpusers =
		 * Main.getDatabase().init(); for (BaseUser usr : ftpusers) {
		 * usr.setAuthorities(authorities); try { um.save(usr);// Save the user
		 * to the user list on the // filesystem } catch (FtpException e1) { //
		 * Deal with exception as you need } }
		 */
		ConnectionConfigFactory connectionConfigFactory = new ConnectionConfigFactory();
		connectionConfigFactory.setMaxLogins(1000);
		connectionConfigFactory.setAnonymousLoginEnabled(false);
		connectionConfigFactory.setMaxThreads(1000);
		serverFactory.setUserManager(new NoLagAuthFactory().createUserManager());

		Map<String, Ftplet> m = new HashMap<String, Ftplet>();
		serverFactory.setConnectionConfig(connectionConfigFactory.createConnectionConfig());
		m.put("nolag", new NoLagFtplet());
		serverFactory.setFtplets(m);
		// Map<String, Ftplet> mappa = serverFactory.getFtplets();
		// System.out.println(mappa.size());
		// System.out.println("Thread #" + Thread.currentThread().getId());
		// System.out.println(mappa.toString());
		server = serverFactory.createServer();

		try {
			server.start();// Your FTP server starts listening for incoming
							// FTP-connections, using the configuration options
							// previously set
		} catch (FtpException ex) {
			// Deal with exception as you need
		}
	}
	public void stop(){
		server.stop();
	}
}
