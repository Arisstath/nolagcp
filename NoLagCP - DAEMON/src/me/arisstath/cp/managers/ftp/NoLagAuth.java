package me.arisstath.cp.managers.ftp;

import org.apache.ftpserver.ftplet.Authentication;
import org.apache.ftpserver.ftplet.AuthenticationFailedException;
import org.apache.ftpserver.ftplet.FtpException;
import org.apache.ftpserver.ftplet.User;
import org.apache.ftpserver.ftplet.UserManager;
import org.apache.ftpserver.usermanager.ClearTextPasswordEncryptor;
import org.apache.ftpserver.usermanager.PasswordEncryptor;
import org.apache.ftpserver.usermanager.UserManagerFactory;
import org.apache.ftpserver.usermanager.UsernamePasswordAuthentication;
import org.apache.ftpserver.usermanager.impl.AbstractUserManager;
import org.apache.ftpserver.usermanager.impl.BaseUser;

import me.arisstath.cp.Main;
import me.arisstath.cp.Main.LogType;
import me.arisstath.cp.obj.srv.MinecraftServer;
import me.arisstath.cp.obj.srv.Service;
import me.arisstath.cp.utils.EncryptionUtils;

class NoLagAuthFactory implements UserManagerFactory {

	@Override
	public UserManager createUserManager() {
		return new NoLagAuth("admin", new ClearTextPasswordEncryptor());
	}
}

class NoLagAuth extends AbstractUserManager {
	private BaseUser testUser;
	private BaseUser anonUser;

	public NoLagAuth(String adminName, PasswordEncryptor passwordEncryptor) {
		super(adminName, passwordEncryptor);

		/*
		 * testUser = new BaseUser(); testUser.setAuthorities(Arrays.asList(new
		 * Authority[] {new ConcurrentLoginPermission(1, 1)}));
		 * testUser.setEnabled(true);
		 * testUser.setHomeDirectory(TEST_USER_FTP_ROOT);
		 * testUser.setMaxIdleTime(10000); testUser.setName(TEST_USERNAME);
		 * testUser.setPassword(TEST_PASSWORD);
		 * 
		 * anonUser = new BaseUser(testUser); anonUser.setName("anonymous");
		 */
	}

	@Override
	public User getUserByName(String username) throws FtpException {
		return (Main.getDatabase().getFTPUser(username));
	}

	@Override
	public String[] getAllUserNames() throws FtpException {
		return Main.getDatabase().getFTPUsers();
	}

	@Override
	public void delete(String username) throws FtpException {
		// no opt
	}

	@Override
	public void save(User user) throws FtpException {
	}

	@Override
	public boolean doesExist(String username) throws FtpException {
		return (Main.getDatabase().doesExist(username));
	}

	@Override
	public User authenticate(Authentication authentication) throws AuthenticationFailedException {
		Main.log("ftp authentication request", LogType.DEBUG);
		if (UsernamePasswordAuthentication.class.isAssignableFrom(authentication.getClass())) {
			UsernamePasswordAuthentication upAuth = (UsernamePasswordAuthentication) authentication;
			String username = upAuth.getUsername();
			String password = upAuth.getPassword();
			//System.out.println("Username: " + username);
			//System.out.println("Password: " + password);
			MinecraftServer server = Main.getDatabase().getServerByFTPUser(username);
			if (server == null) {
				return null;
			}
			Service service = Main.getDatabase().getService(server.getServiceid());
			if(service.getPaid() == 3 || service.getPaid() == 4){
				return null;
			}
			if (server.getFtppass().equals(EncryptionUtils.encrypt(password, "u>M&3gPCUMnc['7S"))) {
				try {
					return getUserByName(username);
				} catch (FtpException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
			}
			server = null; //garbage clean
		}
		return null;
	}
}