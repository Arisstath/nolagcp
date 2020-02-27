package me.arisstath.cp;

import java.awt.Color;
import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.net.URI;
import java.util.Scanner;
import java.util.logging.Level;
import java.util.logging.Logger;

import org.slf4j.LoggerFactory;

import ca.momoperes.canarywebhooks.DiscordMessage;
import ca.momoperes.canarywebhooks.WebhookClient;
import ca.momoperes.canarywebhooks.WebhookClientBuilder;
import ca.momoperes.canarywebhooks.embed.DiscordEmbed;
import me.arisstath.cp.handlers.lists.ServersList;
import me.arisstath.cp.managers.ftp.FTPManager;
import me.arisstath.cp.managers.mysql.Database;
import me.arisstath.cp.managers.web.WebManager;
import me.arisstath.cp.obj.srv.AutoSaveProcess;
import me.arisstath.cp.obj.srv.MinecraftServer;
import me.arisstath.cp.obj.srv.ServerProcess;
import me.arisstath.cp.obj.srv.Service;
import me.arisstath.cp.obj.srv.MinecraftServer.ServerStatus;
import me.arisstath.cp.utils.RuntimeUtils;

public class Main {
	private static Database db;
	private static ServersList slist;
	private static FTPManager ftp;
	private static WebhookClient client;

	private static String node = "";

	public enum LogType {
		DEBUG, INFO, ERROR;
	}
	// It all starts here!
	// CODED BY ARISSTATH

	// CURRENTLY ALLOWED TO BE USED BY NOLAG.HOST BRAND MACHINES UNTIL I STATE (ARISSTATH) OTHERWISE.
	// JUST FOR YOUR INFORMATION, NOLAG IS A BRAND UNDER BOTSTACK PRIVATE HOSTING LTD
	// this servers manager should work at all platforms, even windows and mac,
	// nvm uses screens wont work in windows, nvm it should work now does not
	// use screens

	public static void log(String msg, LogType log) {
		System.out.println("[" + log + "] " + msg);
	}

	private static void executeBash(String cmd) throws Exception {
		String[] cmdd = { "/bin/bash", "-c", cmd };
		Process pb = Runtime.getRuntime().exec(cmdd);

		String line;
		BufferedReader input = new BufferedReader(new InputStreamReader(pb.getInputStream()));
		while ((line = input.readLine()) != null) {
			System.out.println(line);
		}
		input.close();
	}

	public static void main(String[] args) {
		// System.setProperty("org.slf4j.simpleLogger.defaultLogLevel",
		// "DEBUG");
		// TODO: Change this in every release
		PrintWriter verwriter;
		try {
			verwriter = new PrintWriter(new File("version.nolag"));
			verwriter.println("36");
			verwriter.close();
		} catch (FileNotFoundException e1) {
			// TODO Auto-generated catch block
			e1.printStackTrace();
		}
		try {
			executeBash("ulimit -s unlimited");
		} catch (Exception ex) {
			ex.printStackTrace();
		}
		try {
			node = new Scanner(new File("node.txt")).nextLine();
		} catch (FileNotFoundException e) {

			e.printStackTrace();
			System.exit(-1);
		}
		// Download required libraries
		String webhook = "https://discordapp.com/api/webhooks/317025635484565504/59Ejvwa_FQ0A5nw42QiISA3Npot8gMQ1Y_JoCDfVet10tdXiFReerhSlRKHCz0ga-3Z4";
		try {
			client = new WebhookClientBuilder().withURI(new URI(webhook)).build();
		} catch (Exception ex) {
			ex.printStackTrace();
			System.exit(-1);
		}
		slist = new ServersList();
		if (node.toLowerCase().startsWith("us")) {
			db = new Database("144.217.87.50", "cpweb", "pT4MxRFKxTyqgcbC", "nolagcp");
		} else {
			db = new Database("37.59.112.118", "cpweb", "pT4MxRFKxTyqgcbC", "nolagcp");
		}

		sendLogMessage("Starting scheduled tasks...", Color.GRAY);
		new AutoSaveProcess().start();
		sendLogMessage("Scheduled tasks have been started.", Color.GREEN);
		sendLogMessage("Starting FTP server...", Color.GRAY);
		ftp = new FTPManager();
		ftp.start();

		sendLogMessage("FTP server has been started.", Color.GREEN);
		WebManager manager = new WebManager();

		sendLogMessage("Starting webserver...", Color.GRAY);
		manager.start();
		sendLogMessage("Webserver has been started.", Color.GREEN);
		// try for debug purposes

		// db.init();

		sendLogMessage("Daemon has been started and is ready to execute tasks.", Color.GREEN);
		Runtime.getRuntime().addShutdownHook(new Thread() {
			public void run() {
				sendLogMessage("Daemon has been stopped.", Color.RED);
			}
		});

		System.out.println("Welcome to beta version!");
		System.out.println("[+] WELCOME TO NOLAGCP V2 [+]");
		System.out.println("[!] MADE BY ARIS FOR NOLAG [!]");
		System.out.println("Version: Screens fix ok");
		// new ServerProcess().run();
		for (MinecraftServer server : getDatabase().getServers()) {
			Service service = getDatabase().getService(server.getServiceid());
			if (service.getPaid() != 2) {
				continue;
			}
			int sPID = RuntimeUtils.getScreenServerPID(Integer.parseInt(server.getID()));
			if (sPID != -1) {
				System.out.println("Found already running server with PID " + sPID + ", binding...");
				// Then just add the server in the database
				Main.getServersList().addServer(server);
				server.setStatus(ServerStatus.STARTED);
				server.setPid(sPID);

			} else {
				File file = new File(server.getDirectory() + "server.jar");
				if (!file.exists()) {
					continue;
				}
				Main.getServersList().addServer(server);
				server.start();
				System.out.println("Started " + server.getID());

			}
		}

		Scanner scanner = new Scanner(System.in);
		while (true) {
			System.out.print("Type a command > ");
			// scanner.nextLine();
			String cmd = scanner.next();
			System.out.println("Executing > " + cmd);

			if (cmd.equalsIgnoreCase("resetweb")) {
				System.out.println("Resetting web handler...");
				sendLogMessage("Reset webserver has been initiated.", Color.GRAY);
				sendLogMessage("Stopping webserver and clearing queued tasks...", Color.GRAY);
				manager.stopServer();
				manager.start();
				sendLogMessage("Webserver has been started and task queue has been cleared.", Color.GREEN);
				continue;
			}
			if (cmd.equalsIgnoreCase("resetftp")) {
				System.out.println("Resetting ftp handler...");
				sendLogMessage("Reset webserver has been initiated.", Color.GRAY);
				sendLogMessage("Stopping webserver and clearing queued tasks...", Color.GRAY);
				ftp.stop();
				ftp.start();
				sendLogMessage("Webserver has been started and task queue has been cleared.", Color.GREEN);
				continue;
			}
			if (cmd.equalsIgnoreCase("bye")) {
				System.out.println("good bye");
				break;
			}
		}
		scanner.close();
		System.exit(0);
		// start all servers in db, really

	}

	public static void sendLogMessage(String messagee, Color color) {
		DiscordEmbed embed = new DiscordEmbed.Builder().withColor(color).withDescription(messagee).build();
		DiscordMessage message = new DiscordMessage.Builder("").withEmbed(embed).withUsername(node).build();
		try {
			client.sendPayload(message);
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}

	public static String getNode() {
		return node;
	}

	public static FTPManager getFTPManager() {
		return ftp;
	}

	public static ServersList getServersList() {
		return slist;
	}

	public static Database getDatabase() {
		return db;
	}
}
