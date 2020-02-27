package me.arisstath.pinger;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.URL;
import java.net.URLConnection;
import java.nio.charset.Charset;
import java.util.ArrayList;

import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import org.json.simple.parser.ParseException;

import ch.jamiete.mcping.MinecraftPing;
import ch.jamiete.mcping.MinecraftPingOptions;
import ch.jamiete.mcping.MinecraftPingReply;

public class Main {
	
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
			throw new RuntimeException("Exception while calling URL:" + myURL, e);
		}

		return sb.toString();
	}
	
	public static void main(String[] args){
		Database db = new Database("46.4.90.149", "root", "mcspamawesome123a&B", "nolagcp");
		ArrayList<String> servers = db.getIPS();
		long highest = 0;
		
		for(String server : servers){
			String IP = server.split(":")[0];
			int port = Integer.parseInt(server.split(":")[1]);
			System.out.print("Pinging " + server + "... ");
			try {
				JSONObject obj = (JSONObject) new JSONParser().parse(callURL("https://mcapi.ca/query/" + IP + ":" + port + "/players"));
				//System.out.println(obj);
				JSONObject players = (JSONObject) obj.get("players");
				if(obj.get("error") != null){
					System.out.println("failed");
					continue;
				}
				if(((long) players.get("online")) > highest){
					highest = (long) players.get("online");
				}
				System.out.println(players.get("online"));
			} catch (ParseException e1) {
				System.out.println("failed");
				continue;
				//e1.printStackTrace();
			}
			
			
		}
		System.out.println("highest is " + highest);
	}
}
