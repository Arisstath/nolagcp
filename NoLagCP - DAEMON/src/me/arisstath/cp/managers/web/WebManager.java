package me.arisstath.cp.managers.web;

import static spark.Spark.get;
import static spark.Spark.port;
import static spark.Spark.stop;

import java.awt.Color;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.PrintWriter;
import java.io.StringWriter;
import java.lang.management.ManagementFactory;
import java.net.URL;
import java.nio.channels.Channels;
import java.nio.channels.ReadableByteChannel;
import java.text.DecimalFormat;
import java.text.NumberFormat;
import java.util.Base64;
import java.util.Map;
import java.util.Scanner;
import java.util.concurrent.ConcurrentHashMap;

import org.eclipse.jetty.server.session.Session;
import org.json.simple.JSONObject;
import org.jutils.jprocesses.JProcesses;
import org.jutils.jprocesses.model.ProcessInfo;

import me.arisstath.cp.Main;
import me.arisstath.cp.obj.srv.MinecraftServer;
import me.arisstath.cp.obj.srv.MinecraftServer.ServerStatus;
import me.arisstath.cp.obj.srv.Service;
import me.arisstath.cp.obj.usr.NoLagCustomer;
import spark.Spark;

public class WebManager {

	String masterToken = "fda875524d35567786690158966be5f667b70e06";

	public boolean checkToken(String token, boolean master) {
		// System.out.println("Token: " + token);
		if (master) {
			if (masterToken.equals(token))
				return true;
			return false;
		}
		if (token.equalsIgnoreCase(masterToken)) {
			// return true;
		}
		// NoLagCustomer customer =
		// Main.getDatabase().getCustomerByToken(token);
		// System.out.println("customer boolean " + (customer == null));
		return (Main.getDatabase().getCustomerByToken(token) != null);
	}

	public String tail(File file, int lines) {
		java.io.RandomAccessFile fileHandler = null;
		try {
			fileHandler = new java.io.RandomAccessFile(file, "r");

			long fileLength = fileHandler.length() - 1;
			StringBuilder sb = new StringBuilder();
			int line = 0;

			for (long filePointer = fileLength; filePointer != -1; filePointer--) {
				fileHandler.seek(filePointer);
				int readByte = fileHandler.readByte();

				if (readByte == 0xA) {
					if (filePointer < fileLength) {
						line = line + 1;
					}
				} else if (readByte == 0xD) {
					if (filePointer < fileLength - 1) {
						line = line + 1;
					}
				}
				if (line >= lines) {
					break;
				}
				sb.append((char) readByte);
			}

			String lastLine = sb.reverse().toString();
			fileHandler.getChannel().close();
			fileHandler.close();
			return lastLine;
		} catch (java.io.FileNotFoundException e) {
			e.printStackTrace();
			return null;
		} catch (java.io.IOException e) {
			e.printStackTrace();
			return null;
		} finally {
			if (fileHandler != null)
				try {
					fileHandler.close();
				} catch (IOException e) {
				}
		}
	}

	public String accessDeny() {
		JSONObject obj = new JSONObject();
		obj.put("success", 0);
		obj.put("msg", "You do not have permission to access this server.");
		return obj.toJSONString();
	}

	public static String bytes2String(long sizeInBytes) {

		NumberFormat nf = new DecimalFormat();
		nf.setMaximumFractionDigits(0);

		return nf.format(sizeInBytes / (1024 * (1024 * (1024)))) + "";

	}

	public void stopServer() {
		stop();
	}

	public WebManager() {
	}

	public void start() {
		port(80);
		if (Main.getNode().toLowerCase().startsWith("us")) {
			Spark.threadPool(100);
		} else {
			Spark.threadPool(50);
		}
		// Spark.webSocket("/chat", ChatWebSocketHandler.class);
		Spark.exception(Exception.class, (exception, request, response) -> {
			StringWriter sw = new StringWriter();
			PrintWriter pw = new PrintWriter(sw);
			exception.printStackTrace(pw);
			String wholemsg = "Exception in Spark:\n" + sw.toString();
			if (wholemsg.length() > 1999) {
				wholemsg = wholemsg.substring(0, 1999);
			}
			Main.sendLogMessage(wholemsg, Color.RED);
			exception.printStackTrace();
		});

		// billing commands
		get(":token/stats", (request, response) -> {
			response.header("Access-Control-Allow-Origin", "*");
			if (!checkToken(request.params(":token"), true)) {
				return accessDeny();
			}
			JSONObject obj = new JSONObject();
			long space = new File("/").getFreeSpace();
			// long memorySize = ((com.sun.management.OperatingSystemMXBean)
			// ManagementFactory.getOperatingSystemMXBean()).getTotalPhysicalMemorySize();
			long memorySize = ((com.sun.management.OperatingSystemMXBean) ManagementFactory.getOperatingSystemMXBean())
					.getFreePhysicalMemorySize();
			obj.put("ssd", Double.parseDouble(bytes2String(space).replace(",", "")));
			memorySize = (memorySize / 1024) / 1024;
			obj.put("ram", memorySize);
			obj.put("allocatedram", Main.getDatabase().getTotalRamUse());

			return obj.toJSONString();
		});
		get(":token/servers", (request, response) -> {
			response.header("Access-Control-Allow-Origin", "*");
			if (!checkToken(request.params(":token"), true)) {
				return accessDeny();
			}
			JSONObject obj = new JSONObject();
			int cnt = 0;
			for (MinecraftServer server : Main.getServersList().toArray()) {
				cnt++;
				JSONObject srv = new JSONObject();
				srv.put("id", server.getID());
				srv.put("ip", server.getIp());
				srv.put("port", server.getPort());
				srv.put("status", server.getStatus().name());
				obj.put(cnt, srv);
			}

			return obj.toJSONString();
		});

		get(":token/servers/:serverid/start", (request, response) -> {
			response.header("Access-Control-Allow-Origin", "*");
			// System.out.println(request.ip());
			if (!checkToken(request.params(":token"), false)) {
				return accessDeny();
			}

			// Check if user really owns this
			NoLagCustomer customer = Main.getDatabase().getCustomerByToken(request.params(":token"));
			String id = request.params(":serverid");
			// System.out.println("id is " + id);
			JSONObject obj = new JSONObject();
			if (!Main.getDatabase().ownsServer(customer.getUsername(), id)) {
				response.status(405);

				obj.put("success", 0);
				obj.put("msg", "You do not have permission to access this server.");
				return obj.toJSONString();
			}
			MinecraftServer server = Main.getDatabase().getServerById(id);
			if (Main.getServersList().containsId(id)) {
				MinecraftServer srv = Main.getServersList().getServer(id);

				if (srv.getStatus() == ServerStatus.STARTED) {
					obj.put("success", 0);
					obj.put("msg", "Server is already started, you need to stop it first.");
					return obj.toJSONString();
				}
				if (srv.getStatus() == ServerStatus.BOOTING) {
					obj.put("success", 0);
					obj.put("msg", "Server is booting...");
					return obj.toJSONString();
				}
				if (srv.getStatus() == ServerStatus.STOPPING) {
					obj.put("success", 0);
					obj.put("msg", "Server is stopping, please wait before starting it.");
					return obj.toJSONString();
				}
				// since it's already here let's kick it and fetch it again

				srv.stop(false); // just make sure
			}
			File dir = new File("/home/mc_" + server.getID());
			if (!dir.exists()) {
				dir.mkdirs();
			}
			File file = new File(server.getDirectory() + "server.jar");
			if (!file.exists()) {
				obj.put("success", 0);
				obj.put("msg", "There is not any server.jar in server's directory. Please install one!");
				return obj.toJSONString();
			}
			Main.getServersList().addServer(server);
			long fileSizeInBytes = file.length();
			// Convert the bytes to Kilobytes (1 KB = 1024 Bytes)
			long fileSizeInKB = fileSizeInBytes / 1024;
			// Convert the KB to MegaBytes (1 MB = 1024 KBytes)
			long fileSizeInMB = fileSizeInKB / 1024;

			Service service = Main.getDatabase().getService(server.getServiceid());
			if (service.getPaid() == 3 || service.getPaid() == 4) {
				obj.put("success", 0);
				obj.put("msg", "Server is suspended!");
				return obj.toJSONString();
			}
			server.start();
			obj.put("success", 1);
			obj.put("msg", "Server will now start!");
			return obj.toJSONString();
		});

		get(":token/servers/:serverid/stop", (request, response) -> {
			response.header("Access-Control-Allow-Origin", "*");
			if (!checkToken(request.params(":token"), false)) {
				return accessDeny();
			}
			// Check if user really owns this
			NoLagCustomer customer = Main.getDatabase().getCustomerByToken(request.params(":token"));
			String id = request.params(":serverid");
			// System.out.println("id is " + id);
			JSONObject obj = new JSONObject();
			if (!Main.getDatabase().ownsServer(customer.getUsername(), id)) {
				response.status(405);

				obj.put("success", 0);
				obj.put("msg", "You do not have permission to access this server.");
				return obj.toJSONString();
			}
			if (!Main.getServersList().containsId(id)) {
				obj.put("success", 0);
				obj.put("msg", "Server has not started.");
				return obj.toJSONString();
			}
			MinecraftServer srv = Main.getServersList().getServer(id);
			if (srv.getStatus() == ServerStatus.STOPPING) {
				obj.put("success", 0);
				obj.put("msg", "Server is stopping, please wait.");
				return obj.toJSONString();
			}
			if (srv.getStatus() == ServerStatus.STOPPED) {
				obj.put("success", 0);
				obj.put("msg", "Server is already stopped.");
				return obj.toJSONString();
			}
			srv.stop(false);
			obj.put("success", 1);
			obj.put("msg", "Server has stopped.");
			return obj.toJSONString();
		});

		get(":token/servers/:serverid/properties", (request, response) -> {
			response.header("Access-Control-Allow-Origin", "*");
			if (!checkToken(request.params(":token"), false)) {
				return accessDeny();
			}
			// Check if user really owns this
			NoLagCustomer customer = Main.getDatabase().getCustomerByToken(request.params(":token"));
			String id = request.params(":serverid");
			// System.out.println("id is " + id);
			JSONObject obj = new JSONObject();
			if (!Main.getDatabase().ownsServer(customer.getUsername(), id)) {
				response.status(405);

				obj.put("success", 0);
				obj.put("msg", "You do not have permission to access this server.");
				return obj.toJSONString();
			}
			MinecraftServer srv = Main.getDatabase().getServerById(id);
			obj.put("success", 1);
			obj.put("msg", "New .jar has been installed. Please wait a bit before starting your server.");
			return obj.toJSONString();
		});

		get(":token/servers/:serverid/download/:url", (request, response) -> {
			response.header("Access-Control-Allow-Origin", "*");
			if (!checkToken(request.params(":token"), false)) {
				return accessDeny();
			}
			// Check if user really owns this
			NoLagCustomer customer = Main.getDatabase().getCustomerByToken(request.params(":token"));
			String id = request.params(":serverid");
			// System.out.println("id is " + id);
			JSONObject obj = new JSONObject();
			if (!Main.getDatabase().ownsServer(customer.getUsername(), id)) {
				response.status(405);

				obj.put("success", 0);
				obj.put("msg", "You do not have permission to access this server.");
				return obj.toJSONString();
			}
			MinecraftServer srv = Main.getDatabase().getServerById(id);
			int serviceid = srv.getServiceid();

			String url = request.params(":url");
			url = new String(Base64.getDecoder().decode(url));
			if (!url.startsWith("https://cdn.nolag.host") && !url.startsWith("http://yivesmirror.com")
					&& !url.startsWith("https://launcher.mojang.com/mc/game/")) {
				obj.put("success", 0);
				obj.put("msg", "You are not allowed to download files from this host.");
				return obj.toJSONString();
			}
			System.setProperty("http.agent",
					"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36");
			URL website = new URL(url);
			File dir = new File("/home/mc_" + srv.getID());
			if (!dir.exists()) {
				dir.mkdirs();
			}
			ReadableByteChannel rbc = Channels.newChannel(website.openStream());
			FileOutputStream fos = new FileOutputStream("/home/mc_" + srv.getID() + "/server.jar");
			fos.getChannel().transferFrom(rbc, 0, Long.MAX_VALUE);
			fos.close();

			Service service = Main.getDatabase().getService(serviceid);
			// System.out.println("getpaid = " + service.getPaid());
			if (service.getPaid() == 1) {
				Main.getDatabase().setStatus(service, 2);

			}

			obj.put("success", 1);
			obj.put("msg", "New .jar has been installed. Please wait a bit before starting your server.");
			return obj.toJSONString();
		});

		get(":token/servers/:serverid/command/:cmd", (request, response) -> {
			response.header("Access-Control-Allow-Origin", "*");
			if (!checkToken(request.params(":token"), false)) {
				return accessDeny();
			}
			// Check if user really owns this
			NoLagCustomer customer = Main.getDatabase().getCustomerByToken(request.params(":token"));
			String id = request.params(":serverid");
			// System.out.println("id is " + id);
			JSONObject obj = new JSONObject();
			if (!Main.getDatabase().ownsServer(customer.getUsername(), id)) {
				response.status(405);

				obj.put("success", 0);
				obj.put("msg", "You do not have permission to access this server.");
				return obj.toJSONString();
			}
			if (!Main.getServersList().containsId(id)) {
				obj.put("success", 0);
				obj.put("msg", "Server has not started.");
				return obj.toJSONString();
			}
			MinecraftServer srv = Main.getServersList().getServer(id);
			if (srv.getStatus() != ServerStatus.STARTED) {
				obj.put("success", 0);
				obj.put("msg", "You can only send commands when the server is started.");
				return obj.toJSONString();
			}
			srv.sendCommand(request.params(":cmd"));
			obj.put("success", 1);
			obj.put("msg", "Command has been executed.");
			return obj.toJSONString();
		});
		get(":token/servers/:serverid/status", (request, response) -> {
			response.header("Access-Control-Allow-Origin", "*");
			if (!checkToken(request.params(":token"), false)) {
				return accessDeny();
			}
			// Check if user really owns this
			NoLagCustomer customer = Main.getDatabase().getCustomerByToken(request.params(":token"));
			String id = request.params(":serverid");
			// System.out.println("id is " + id);
			JSONObject obj = new JSONObject();
			if (!Main.getDatabase().ownsServer(customer.getUsername(), id)) {
				response.status(405);
				obj.put("success", 0);
				obj.put("status", "STOPPED");
				return obj.toJSONString();
			}
			if (!Main.getServersList().containsId(id)) {
				obj.put("success", 0);
				obj.put("status", "STOPPED");
				return obj.toJSONString();
			}
			MinecraftServer srv = Main.getServersList().getServer(id);
			obj.put("success", 1);
			obj.put("status", srv.getStatus().name());
			return obj.toJSONString();
		});
		get(":token/servers/:serverid/stats", (request, response) -> {
			response.header("Access-Control-Allow-Origin", "*");
			if (!checkToken(request.params(":token"), false)) {
				return accessDeny();
			}
			// Check if user really owns this
			NoLagCustomer customer = Main.getDatabase().getCustomerByToken(request.params(":token"));
			String id = request.params(":serverid");
			// System.out.println("id is " + id);
			JSONObject obj = new JSONObject();
			if (!Main.getDatabase().ownsServer(customer.getUsername(), id)) {
				obj.put("success", 0);
				obj.put("msg", "You do not have permission to access this server.");
				obj.put("status", "STOPPED");
				return obj.toJSONString();
			}
			if (!Main.getServersList().containsId(id)) {
				obj.put("success", 0);
				obj.put("msg", "Server has not started or there isn't any version installed");
				return obj.toJSONString();
			}

			MinecraftServer srv = Main.getServersList().getServer(id);
			if (!new File(srv.getDirectory() + "/nolagcp/output.log").exists()) {
				return "Could not find any logs, please start your server first.";
			}
			File file = new File(srv.getDirectory() + "/nolagcp/info.txt");

			double tps = 0;
			int ram = 0;
			int online = 0;
			String players = "Not Bukkit";
			if (file.exists()) {
				Scanner scanner = new Scanner(file);
				while (scanner.hasNextLine()) {
					String line = scanner.nextLine();
					String type = line.split(":")[0];
					if (type.equals("ram")) {
						ram = Integer.parseInt(line.split(":")[1]);
					}
					if (type.equals("online")) {
						online = Integer.parseInt(line.split(":")[1]);
					}
					if (type.equals("tps")) {
						tps = Double.parseDouble(line.split(":")[1]);
						DecimalFormat df = new DecimalFormat("#.##");
						tps = Double.valueOf(df.format(tps));
					}
					if (type.equals("players")) {
						if (line.split(":").length > 1) {
							players = line.split(":")[1];
						}
					}
				}
				scanner.close();
			}

			String logs = tail(new File(srv.getDirectory() + "/nolagcp/output.log"), 1000);
			obj.put("tps", tps);
			obj.put("ram", ram);
			obj.put("online", online);
			obj.put("players", players);
			obj.put("status", "" + srv.getStatus());
			obj.put("logs", JSONObject.escape(logs));
			// response.header("Access-Control-Allow-Origin", "*");
			return obj.toJSONString();
		});
		get(":token/servers/:serverid/stats", (request, response) -> {
			response.header("Access-Control-Allow-Origin", "*");
			if (!checkToken(request.params(":token"), false)) {
				return accessDeny();
			}
			// Check if user really owns this
			NoLagCustomer customer = Main.getDatabase().getCustomerByToken(request.params(":token"));
			String id = request.params(":serverid");
			// System.out.println("id is " + id);
			JSONObject obj = new JSONObject();
			if (!Main.getDatabase().ownsServer(customer.getUsername(), id)) {
				response.status(405);

				return "You don't have permission to access this server.";
			}
			if (!Main.getServersList().containsId(id)) {
				return "Server has not started or there isn't any version installed";
			}

			MinecraftServer srv = Main.getServersList().getServer(id);
			if (!new File(srv.getDirectory() + "/nolagcp/output.log").exists()) {
				return "Could not find any logs, please start your server first.";
			}
			File file = new File(srv.getDirectory() + "/nolagcp/info.txt");

			double tps = 0;
			int ram = 0;
			int online = 0;
			String players = "Not Bukkit";
			if (file.exists()) {
				Scanner scanner = new Scanner(file);
				while (scanner.hasNextLine()) {
					String line = scanner.nextLine();
					String type = line.split(":")[0];
					if (type.equals("ram")) {
						ram = Integer.parseInt(line.split(":")[1]);
					}
					if (type.equals("online")) {
						online = Integer.parseInt(line.split(":")[1]);
					}
					if (type.equals("tps")) {
						tps = Double.parseDouble(line.split(":")[1]);
						DecimalFormat df = new DecimalFormat("#.##");
						tps = Double.valueOf(df.format(tps));
					}
					if (type.equals("players")) {
						if (line.split(":").length > 1) {
							players = line.split(":")[1];
						}
					}
				}
				scanner.close();
			}

			String logs = tail(new File(srv.getDirectory() + "/nolagcp/output.log"), 1000);
			obj.put("tps", tps);
			obj.put("ram", ram);
			obj.put("online", online);
			obj.put("players", players);
			obj.put("status", "" + srv.getStatus());
			obj.put("logs", JSONObject.escape(logs));
			// response.header("Access-Control-Allow-Origin", "*");
			return obj.toJSONString();
		});

		get("testexception/", (request, response) -> {
			throw new Exception("test");
		});
	}

}
