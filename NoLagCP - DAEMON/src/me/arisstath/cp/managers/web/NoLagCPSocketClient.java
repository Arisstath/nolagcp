package me.arisstath.cp.managers.web;

import me.arisstath.cp.obj.srv.MinecraftServer;
import me.arisstath.cp.obj.usr.NoLagCustomer;

public class NoLagCPSocketClient {

	MinecraftServer server;
	String token;
	NoLagCustomer client;
	
	
	public MinecraftServer getServer() {
		return server;
	}
	public void setServer(MinecraftServer server) {
		this.server = server;
	}
	public String getToken() {
		return token;
	}
	public void setToken(String token) {
		this.token = token;
	}
	public NoLagCustomer getClient() {
		return client;
	}
	public void setClient(NoLagCustomer client) {
		this.client = client;
	}
	
	
}
