package me.arisstath.cp.handlers.lists;

import java.awt.Color;
import java.util.ArrayList;
import java.util.Queue;
import java.util.concurrent.ConcurrentLinkedQueue;

import me.arisstath.cp.Main;
import me.arisstath.cp.obj.srv.MinecraftServer;

public class ServersList {

	public Queue<MinecraftServer> servers = new ConcurrentLinkedQueue<>();

	public  void addServer(MinecraftServer srv) {
		// check duplicates
		if (!servers.contains(srv))
			servers.add(srv);
		else
			Main.sendLogMessage("Hah, duplicate entry was detected.", Color.RED);
	}

	public boolean containsId(String srv) {
		for (MinecraftServer server : servers) {
			if (srv.equals(server.getID()))
				return true;
		}

		return false;
	}

	public Queue<MinecraftServer> toArray() {
		return servers;
	}

	public  void removeServer(MinecraftServer srv) {
		if (servers.contains(srv))
			servers.remove(srv);
	}

	public MinecraftServer getServer(String id) {
		for (MinecraftServer server : servers) {
			if (server.getID().equals(id))
				return server;
		}
		return null;
	}
}
