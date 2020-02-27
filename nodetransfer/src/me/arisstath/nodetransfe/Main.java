package me.arisstath.nodetransfe;

import java.awt.Color;
import java.io.BufferedReader;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStreamReader;
import java.lang.reflect.Field;
import java.lang.reflect.Modifier;
import java.net.URL;
import java.net.URLConnection;
import java.nio.charset.Charset;
import java.util.Scanner;

import com.jcraft.jsch.Channel;
import com.jcraft.jsch.ChannelSftp;
import com.jcraft.jsch.JSch;
import com.jcraft.jsch.Session;
import com.jcraft.jsch.SftpATTRS;
import com.jcraft.jsch.SftpException;

import ca.momoperes.canarywebhooks.DiscordMessage;
import ca.momoperes.canarywebhooks.WebhookClient;
import ca.momoperes.canarywebhooks.embed.DiscordEmbed;

public class Main {
	public static String node = "";

	private static WebhookClient client;

	// Screw "paid apis"

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

	public static String callURL(String myURL) {
		// System.out.println("Requeted URL:" + myURL);
		StringBuilder sb = new StringBuilder();
		URLConnection urlConn = null;
		InputStreamReader in = null;
		try {
			URL url = new URL(myURL);
			urlConn = url.openConnection();
			urlConn.setRequestProperty("user-agent",
					"Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Googlebot/2.1; +http://www.google.com/bot.html) Safari/537.36.");
			if (urlConn != null)
				urlConn.setReadTimeout(60 * 1000);
			if (urlConn != null && urlConn.getInputStream() != null) {
				in = new InputStreamReader(urlConn.getInputStream(), Charset.defaultCharset());
				BufferedReader bufferedReader = new BufferedReader(in);
				if (bufferedReader != null) {
					int cp;
					while ((cp = bufferedReader.read()) != -1) {
						sb.append((char) cp);
					}
					bufferedReader.close();
				}
			}
			in.close();
		} catch (Exception e) {
			e.printStackTrace();
		}

		return sb.toString();
	}

	public static void transfer(Database db, String targetIP, String targetNode, int targetServer) {
		try {
			if (!db.validateOwnership(targetServer)) {
				System.out.println("[-] The specified server(" + targetServer + ") is not here.");
				System.exit(-1);
			}
			System.out.print("[+] Calculating the server directory... ");
			String directory = db.getDirectory(targetServer);
			System.out.print(directory);
			System.out.println();
			if (!new File(directory).exists()) {
				System.out.println("[-] The server directory could not be found in this node.");
				System.exit(-1);
			}
			System.out.println("==========[TRANSFER JOB]==========");
			System.out.println("Local node: " + node);
			System.out.println("Target IP: " + targetIP);
			System.out.println("Directory: " + directory);
			System.out.println("Target node: " + targetNode);
			System.out.println("==========[TRANSFER JOB]==========");
			System.out.println("Starting transfer job...");
			JSch jsch = new JSch();

			jsch.addIdentity("nodemanage.key", "3OSxxwD33fOdpXka9xw*");

			Session session = jsch.getSession("root", targetIP, 22);

			java.util.Properties config = new java.util.Properties();
			config.put("StrictHostKeyChecking", "no");
			session.setConfig(config);

			session.connect();
			Channel channel = session.openChannel("sftp");
			channel.connect();
			ChannelSftp channelSftp = (ChannelSftp) channel;
			channelSftp.mkdir(directory);
			channelSftp.cd(directory);
			System.out.println("Connected to " + node + ": " + session.getServerVersion());
			System.out.println("Uploading...");
			recursiveFolderUpload(channelSftp, directory, "/home");
			System.out.println("Uploaded. Updating MySQL...");
			db.updateServer(targetServer, targetNode, targetIP);

		} catch (Exception ex) {
			ex.printStackTrace();
		}
	}
	public static final String C = "memes";
	public static void setFinalStatic(Field field, Object newValue) throws Exception {
	      field.setAccessible(true);

	      Field modField = Field.class.getDeclaredField("modifiers");
	      modField.setAccessible(true);
	      modField.setString(field, field.getModifiers() & ~Modifier.FINAL);

	      field.set(null, newValue);
	   }
	
	public static void main(String[] arg) {
		try {
			node = new Scanner(new File("node.txt")).nextLine();
		} catch (FileNotFoundException e1) {
			// TODO Auto-generated catch block
			e1.printStackTrace();
		}
		//node = "EU1"; //debug
		System.out.println("Node is " + node);
		Database db = new Database("217.182.72.100", "cpweb", "pT4MxRFKxTyqgcbC", "nolagcp");
		while (true) {
			System.out.println("Checking for pending jobs...");
			try {
				for (TransferJob transferJob : db.getPendingTransfers()) {
					db.updateJob(transferJob.id, "RUNNING");
					transfer(db, transferJob.getTargetIP(), transferJob.getTargetNode(), transferJob.getTargetServer());
					db.updateJob(transferJob.id, "FINISHED");
				}
			} catch (Exception e) {
				e.printStackTrace();
			}
			try {
				Thread.sleep(1000 * 30);
			} catch (InterruptedException e) {
				e.printStackTrace();
			}
		}

		/*
		 * String id = ""; String node = ""; String webhook =
		 * "https://discordapp.com/api/webhooks/348192317712105472/voUym0CmNYI3sEqgw_L9fB38F6mg-AsxpyG9dLhC6M4ZmKTtCUoFadBk6sRn4Lc1HRij";
		 * try { client = new WebhookClientBuilder().withURI(new
		 * URI(webhook)).build(); } catch (Exception ex) { ex.printStackTrace();
		 * System.exit(-1); } Database db = new Database("217.182.72.100",
		 * "cpweb", "pT4MxRFKxTyqgcbC", "nolagcp"); Scanner scanner = new
		 * Scanner(System.in); System.out.println("[+] Detecting node..."); try
		 * { node = new Scanner(new File("node.txt")).nextLine(); } catch
		 * (FileNotFoundException e) { // TODO Auto-generated catch block
		 * e.printStackTrace(); System.exit(-1); } System.out.println(
		 * "[+] This node has been detected as " + node); System.out.println(
		 * "    _   __      __               "); System.out.println(
		 * "   / | / /___  / /   ____ _____ _"); System.out.println(
		 * "  /  |/ / __ \\/ /   / __ `/ __ `/"); System.out.println(
		 * " / /|  / /_/ / /___/ /_/ / /_/ / "); System.out.println(
		 * "/_/ |_/\\____/_____/\\__,_/\\__, /  "); System.out.println(
		 * "                        /____/   "); System.out.println(
		 * "Node Transfer, BOTSTACK PRIVATE HOSTING LTD, All Rights Reserved");
		 * String daemonToken = ""; try { daemonToken = db.getDaemonToken(0); }
		 * catch (Exception e) { // TODO Auto-generated catch block
		 * e.printStackTrace(); } System.out.println("Got daemon token, " +
		 * daemonToken); System.out.print("Please type the target server ID > "
		 * ); int targetServer = -1; String input = scanner.nextLine(); if
		 * (!input.equals("*")) { targetServer = Integer.parseInt(input); }
		 * PrintWriter writer = null; try { writer = new PrintWriter(new
		 * File("/root/transfer.log")); } catch (FileNotFoundException e) { //
		 * TODO Auto-generated catch block e.printStackTrace(); }
		 * System.out.println(); System.out.print(
		 * "Now type the target node(EU1,EU2,EU3,etc) > ");
		 * System.out.println(); String targetNode =
		 * scanner.nextLine().toUpperCase(); System.out.println(
		 * "[+] Please wait, validating parameters..."); String targetIP =
		 * callURL("http://45.43.7.44/ipresolver.php?server=" + targetNode); if
		 * (targetIP.isEmpty()) { System.out.println(
		 * "[-] Could not resolve the IP of the target node."); System.exit(-1);
		 * } if(targetServer == -1){
		 * System.out.println("\n\n\n==================================");
		 * System.out.println("You have selected ALL the servers at this nodes."
		 * ); System.out.println(
		 * "This means that all of the servers will be moved.");
		 * System.out.println(
		 * "This can take hours to be completed, and the target server each time will be down for minutes."
		 * ); System.out.println("Be careful!");
		 * System.out.println("==================================");
		 * 
		 * ArrayList<MCServer> minecraftServers = null; try { minecraftServers =
		 * db.getAllServers(); } catch (Exception e) { // TODO Auto-generated
		 * catch block e.printStackTrace(); } int current = 0; for(MCServer
		 * server : minecraftServers){ try{ current++; System.out.println(
		 * "Job for #" + server.getId()); sendLogMessage(
		 * "Transferring server **" + current + "/" + minecraftServers.size() +
		 * "**", Color.CYAN); if (!db.validateOwnership(server.getId())) {
		 * System.out.println("[-] The specified server(" + server.getId() +
		 * ") is not here."); continue; } String directory =
		 * db.getDirectory(server.getId()); if (!new File(directory).exists()) {
		 * System.out.println(
		 * "[-] The server directory could not be found in this node.");
		 * continue; } System.out.println("==========[TRANSFER JOB]==========");
		 * System.out.println("Local node: " + node); System.out.println(
		 * "Target node: " + targetNode); System.out.println("Target IP: " +
		 * targetIP); System.out.println("Directory: " + directory);
		 * System.out.println("==========[TRANSFER JOB]==========");
		 * System.out.println("Do you want to continue? (y/n) (OVERRIDE)");
		 * System.out.println("Starting transfer job..."); System.out.println(
		 * "Shutting down server...");
		 * 
		 * JSch jsch = new JSch();
		 * 
		 * jsch.addIdentity("nodemanage.key", "3OSxxwD33fOdpXka9xw*"); Session
		 * session = jsch.getSession("root", targetIP, 22);
		 * 
		 * java.util.Properties config = new java.util.Properties();
		 * config.put("StrictHostKeyChecking", "no"); session.setConfig(config);
		 * 
		 * session.connect(); Channel channel = session.openChannel("sftp");
		 * channel.connect(); ChannelSftp channelSftp = (ChannelSftp) channel;
		 * String username = db.getUsername(targetServer);
		 * channelSftp.mkdir(directory); channelSftp.cd(directory);
		 * System.out.println("Connected to " + node + ": " +
		 * session.getServerVersion()); System.out.println("Uploading...");
		 * recursiveFolderUpload(channelSftp, directory, "/home");
		 * System.out.println("Uploaded. Updating MySQL...");
		 * db.updateServer(server.getId(), targetNode, targetIP);
		 * writer.println(server.getId() + " => " + targetNode);
		 * System.out.println(callURL("https://" + targetNode + ".mcsrv.top/" +
		 * daemonToken + "/servers/" + server.getId() + "/start"));
		 * sendLogMessage("Server transferred to " + targetNode, Color.GREEN); }
		 * catch (Exception ex){
		 * 
		 * ex.printStackTrace(); continue; } } writer.close(); return; }
		 * transfer(db, targetIP, targetNode, targetServer); // ascii art heart
		 * System.out.println(
		 * "       XOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOX\r\n       O:::::::::::::::::::::::::::::::::::::::::::::::::::::::O\r\n       X:::::::::::::::::::::::::::::::::::::::::::::::::::::::X\r\n       O::::::::::::           :::::::::           ::::::::::::O\r\n       X:::::::::                :::::                :::::::::X\r\n       O:::::::       *********    :    *********       :::::::O\r\n       X:::::      *****     *****   *****     *****      :::::X\r\n       O::::     ****           *******           ****     ::::O\r\n       X:::     ****              ***              ****     :::X\r\n       O:::     ****               *               ****     :::O\r\n       X::::     ****                             ****     ::::X\r\n       O:::::     ****                           ****     :::::O\r\n       X:::::::     ****                       ****     :::::::X\r\n       O:::::::::     ****                   ****     :::::::::O\r\n       X:::::::::::     ****               ****     :::::::::::X\r\n       O::::::::::::::     ****         ****     ::::::::::::::O\r\n       X:::::::::::::::::     ****   ****     :::::::::::::::::X\r\n       O::::::::::::::::::::     *****     ::::::::::::::::::::O\r\n       X:::::::::::::::::::::::    *    :::::::::::::::::::::::X\r\n       O:::::::::::::::::::::::::     :::::::::::::::::::::::::O\r\n       X::::::::::::::::::::::::::: :::::::::::::::::::::::::::X\r\n       O:::::::::::::::::::::::::::::::::::::::::::::::::::::::O\r\n       X:::::::::::::::::::::::::::::::::::::::::::::::::::::::X\r\n       OXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXOXO"
		 * );
		 */
	}

	private static void recursiveFolderUpload(ChannelSftp channelSftp, String sourcePath, String destinationPath)
			throws SftpException, FileNotFoundException {

		File sourceFile = new File(sourcePath);
		if (sourceFile.isFile()) {

			// copy if it is a file
			channelSftp.cd(destinationPath);
			if (!sourceFile.getName().startsWith("."))
				channelSftp.put(new FileInputStream(sourceFile), sourceFile.getName(), ChannelSftp.OVERWRITE);

		} else {

			System.out.println("inside else " + sourceFile.getName());
			File[] files = sourceFile.listFiles();

			if (files != null && !sourceFile.getName().startsWith(".")) {

				channelSftp.cd(destinationPath);
				SftpATTRS attrs = null;

				// check if the directory is already existing
				try {
					attrs = channelSftp.stat(destinationPath + "/" + sourceFile.getName());
				} catch (Exception e) {
					System.out.println(destinationPath + "/" + sourceFile.getName() + " not found");
				}

				// else create a directory
				if (attrs != null) {
					// System.out.println("Directory exists IsDir=" +
					// attrs.isDir());
				} else {
					// System.out.println("Creating dir " +
					// sourceFile.getName());
					channelSftp.mkdir(sourceFile.getName());
				}

				for (File f : files) {
					recursiveFolderUpload(channelSftp, f.getAbsolutePath(),
							destinationPath + "/" + sourceFile.getName());
				}

			}
		}

	}

}