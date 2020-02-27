package me.arisstath.nolagwrappe;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.InputStreamReader;
import java.net.Authenticator;
import java.net.PasswordAuthentication;
import java.net.URL;
import java.net.URLConnection;
import java.nio.channels.Channels;
import java.nio.channels.ReadableByteChannel;
import java.nio.charset.Charset;
import java.util.Scanner;

import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;

public class Main {

	public static Process nolagcpprocess = null;
	public static boolean checkUpdate = true;
	public static String node = "";
	public static long startTime = 0;

	public static String callURL(String myURL) {
		// System.out.println("Requeted URL:" + myURL);
		StringBuilder sb = new StringBuilder();
		URLConnection urlConn = null;
		InputStreamReader in = null;
		try {
			URL url = new URL(myURL);

			urlConn = url.openConnection();
			// User-Agent is for cloudflare
			urlConn.addRequestProperty("User-Agent",
					"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.95 Safari/537.11");
			urlConn.connect();
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
			// throw new RuntimeException("Exception while calling URL:" +
			// myURL, e);
			e.printStackTrace();
			return "";
			// System.exit(-1);
		}

		return sb.toString();
	}

	public static void isDown() {
		System.out.println("============================");
		System.out.println("Checking if down....");
		if (!callURL("http://" + node.toLowerCase() + ".mcsrv.top").contains("404")) {
			long runningFor = System.currentTimeMillis() - startTime;
			System.out.println("Daemon is running for " + runningFor + "ms.");
			// WE GIVE IT 2 MINUTES TO BOOT, OK?
			if (runningFor > (1000 * 60 * 2)) {
				// SHIT SHIT SHIT SHIT SHIT SHIT NODE IS DOWN ALERT ALERT ALERT
				new File("version.nolag").delete(); // THIS WILL FORCE THAT SHIT
													// TO RESTART
				System.out.println("Daemon will now restart.");
				System.out.println("============================");
				System.out.println("\n\n");
			} else {
				System.out.println("Check skipped, it is not up for enough time.");
				System.out.println("============================");
				System.out.println("\n\n");
			}
			return;
		}
		System.out.println("Daemon is up.");
		System.out.println("============================");
		System.out.println("\n\n");

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

	public static void update() {
		System.out.println("Checking for updates...");
		try {
			String jsonUpdate = Main.callURL("http://45.43.7.44/static/ff/update.json");
			JSONObject obj = (JSONObject) new JSONParser().parse(jsonUpdate);
			String version = (String) obj.get("latestVersion");
			String fileName = (String) obj.get("fileName");
			if (new File("version.nolag").exists()) {
				Scanner scanner = new Scanner(new File("version.nolag"));
				String versionf = scanner.next();
				System.out.println("Version on file " + versionf);
				if (versionf.equals(version)) {
					System.out.println("No new updates found.");
					scanner.close();
					return;
				}
				scanner.close();
			}

			if (nolagcpprocess != null) {
				nolagcpprocess.destroyForcibly();
				int pid = RuntimeUtils.getPidP(nolagcpprocess);
				executeBash("kill -9 " + pid);
			}
			startTime = System.currentTimeMillis();
			nolagcpprocess = null;
			// kill all java processes just to be sure
			// this will kill itself idk
			System.out.println("New NoLagCP version has been found. Version is #" + version);
			new File("nolagcp.jar").delete();
			System.out.println("Downloading...");
			URL website = new URL("http://45.43.7.44/static/ff/" + fileName);

			ReadableByteChannel rbc = Channels.newChannel(website.openStream());
			FileOutputStream fos = new FileOutputStream("nolagcp.jar");
			fos.getChannel().transferFrom(rbc, 0, Long.MAX_VALUE);
			fos.close();
			System.out.println("New nolagcp.jar has been downloaded.");
			// you can start it
			ProcessBuilder builder = new ProcessBuilder("java", "-jar", fileName);
			builder = builder.inheritIO();

			nolagcpprocess = builder.start();
			System.out.println("It has been launched successfully.");
		} catch (Exception ex) {
			ex.printStackTrace();
		}
	}

	public static void main(String[] args) throws FileNotFoundException {
		Authenticator.setDefault(new Authenticator() {
			protected PasswordAuthentication getPasswordAuthentication() {
				return new PasswordAuthentication("nolagcp", "%WDvQ~dD3bj.MdH-".toCharArray());
			}
		});

		System.out.println("Welcome to NoLagCP Wrapper.");
		Scanner nodeScanner = new Scanner(new File("node.txt"));
		node = nodeScanner.nextLine();
		nodeScanner.close();
		System.out.println("Detected node is " + node);
		System.out.println("Down check update");
		try {
			Thread.sleep(1000);
		} catch (InterruptedException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		// check update first
		new File("version.nolag").delete(); // to force update
		new UpdateCheck().run();
		//new DownCheck().run();
	}
}
