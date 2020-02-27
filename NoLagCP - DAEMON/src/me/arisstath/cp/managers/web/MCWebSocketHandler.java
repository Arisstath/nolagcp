package me.arisstath.cp.managers.web;

import java.io.IOException;
import java.util.Map;
import java.util.concurrent.ConcurrentHashMap;

import org.eclipse.jetty.websocket.api.Session;
import org.eclipse.jetty.websocket.api.annotations.OnWebSocketClose;
import org.eclipse.jetty.websocket.api.annotations.OnWebSocketConnect;
import org.eclipse.jetty.websocket.api.annotations.OnWebSocketMessage;
import org.eclipse.jetty.websocket.api.annotations.WebSocket;
import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import org.json.simple.parser.ParseException;

import me.arisstath.cp.Main;
import me.arisstath.cp.Main.LogType;
import me.arisstath.cp.obj.srv.MinecraftServer;
import me.arisstath.cp.obj.usr.NoLagCustomer;

@WebSocket
public class MCWebSocketHandler {

	// private static final Queue<Session> sessions = new
	// ConcurrentLinkedQueue<>();
	public static Map<Session, NoLagCPSocketClient> sessions = new ConcurrentHashMap<>();

	@OnWebSocketConnect
	public void connected(Session session) throws IOException {

		sessions.put(session, new NoLagCPSocketClient());
		session.getRemote().sendString("HELLO");
	}

	@OnWebSocketClose
	public void closed(Session session, int statusCode, String reason) {
		sessions.remove(session);
	}

	@OnWebSocketMessage
	public void message(Session session, String message) throws IOException, ParseException {
		Main.log("Got: " + message, LogType.DEBUG);
		JSONObject messageJSON = (JSONObject) new JSONParser().parse(message);
		NoLagCPSocketClient sNolagger = sessions.get(session);
		if (messageJSON.get("scope").toString().equals("token_auth")) {
			
			sNolagger.setToken(messageJSON.get("token").toString());
			// Validate
			NoLagCustomer customer = Main.getDatabase().getCustomerByToken(sNolagger.getToken());
			if(customer == null){
				session.getRemote().sendString("AUTH_DENIED");
				session.close();
				return;
			} else {
				sNolagger.setClient(customer);
				session.getRemote().sendString("SERVERID");
			}
		}
		if(sNolagger.getClient() != null){
			if (message.startsWith("SERVERID_")) {
				MinecraftServer server = Main.getDatabase().getServerById(message.split("_")[1]);
				
			}
		}
	}

}