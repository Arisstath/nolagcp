package me.arisstath.cp.obj.usr;

import java.util.ArrayList;

import me.arisstath.cp.obj.srv.MinecraftServer;

public class NoLagCustomer {

	String email, username, token;
	ArrayList<MinecraftServer> servers;
	

	

	public String getToken() {
		return token;
	}
	public void setToken(String token) {
		this.token = token;
	}
	public String getEmail() {
		return email;
	}
	public void setEmail(String email) {
		this.email = email;
	}
	public String getUsername() {
		return username;
	}
	public void setUsername(String username) {
		this.username = username;
	}
	public ArrayList<MinecraftServer> getServers() {
		return servers;
	}
	public void setServers(ArrayList<MinecraftServer> servers) {
		this.servers = servers;
	}
	
	
}
