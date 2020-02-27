package me.arisstath.cp.obj.srv;

import java.io.File;
import java.io.PrintWriter;
import java.util.HashMap;
import java.util.Scanner;

public class ServerPropertiesParser {
	private HashMap<String, String> properties = new HashMap<>();
	private File serverPropertiesFile;

	public ServerPropertiesParser(File file) throws Exception {
		serverPropertiesFile = file;
		Scanner scanner = new Scanner(file);
		while (scanner.hasNextLine()) {
			String line = scanner.nextLine();
			if (line.startsWith("#"))
				continue; // We do not want to parse comments
			if (line.split("=").length != 2)
				continue; // Not a valid line
			String[] property = line.split("=");
			properties.put(property[0], property[1]);
		}
		scanner.close(); // Avoid resource leaks
	}

	public void setProperty(String property, String value) {
		properties.put(property, value);
	}

	public void saveProperties() throws Exception {
		if (serverPropertiesFile == null) {
			throw new NullPointerException("Server properties file is null!");
		}
		PrintWriter writer = new PrintWriter(serverPropertiesFile);
		writer.println("#Generated by NoLagCP");
		properties.forEach((k, v) -> {
			writer.println(k + "=" + v); // write each property to the file
		});
		writer.close();
	}

	/**
	 * Disable breaking blocks from the area that is x blocks away
	 *
	 * @param distance
	 *            Distance measured in blocks
	 */
	public void setSpawnProtection(int distance) {
		setProperty("spawn-protection", String.valueOf(distance));
	}

	/**
	 * If all users, when they join, get the default gamemode
	 *
	 * @param forceGamemode
	 *            If user should get the default gamemode on join
	 */
	public void forceGamemode(boolean forceGamemode) {
		setProperty("force-gamemode", String.valueOf(forceGamemode));
	}

	/**
	 * Default gamemode, that players will get it when they join at the first
	 * time
	 *
	 * @param gamemode
	 *            0 for survival, 1 for creative, 2 for hardcore
	 */
	public void defaultGamemode(int gamemode) {
		setProperty("gamemode", String.valueOf(gamemode));
	}

	/**
	 * Maximum online players allowed at the server
	 *
	 * @param size
	 *            How many players are allowed at the server
	 */
	public void maxPlayers(int size) {
		setProperty("max-players", String.valueOf(size));
	}

	/**
	 * If "cracked" players are allowed to join the game
	 *
	 * @param boolean
	 *            Are non-authenticated with Minecraft players allowed to join
	 */
	public void onlineMode(boolean onlineMode) {
		setProperty("online-mode", String.valueOf(onlineMode));
	}

	/**
	 * Maximum y where the players can place blocks
	 *
	 * @param int
	 *            Maximum y
	 */
	public void maxBuildHeight(int height) {
		setProperty("max-build-height", String.valueOf(height));
	}
	
	/**
	 * The IP of the server
	 *
	 * @param String
	 *            IP of the server
	 */
	public void ip(String ip) {
		setProperty("server-ip", ip);
	}
	
	/**
	 * The port of the server
	 *
	 * @param int
	 *            port of the server
	 */
	public void port(int port) {
		setProperty("server-port", String.valueOf(port));
	}
}
