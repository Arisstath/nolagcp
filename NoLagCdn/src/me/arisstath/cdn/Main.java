package me.arisstath.cdn;

import static spark.Spark.get;
import static spark.Spark.port;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.net.URL;
import java.net.URLConnection;
import java.net.URLEncoder;
import java.nio.charset.Charset;
import java.util.HashMap;
import java.util.Iterator;

import javax.net.ssl.HttpsURLConnection;
import javax.net.ssl.SSLContext;
import javax.net.ssl.TrustManager;
import javax.net.ssl.X509TrustManager;

import org.json.JSONArray;
import org.json.JSONObject;

import spark.Spark;

public class Main {

	public static HashMap<String, String> versions = new HashMap<>();
	
	public static void main(String[] args) {
		
		
		
		versions.put("1.11.2","https://launcher.mojang.com/mc/game/1.11.2/server/f00c294a1576e03fddcac777c3cf4c7d404c4ba4/server.jar");
		versions.put("1.11.1", "https://launcher.mojang.com/mc/game/1.11.1/server/1f97bd101e508d7b52b3d6a7879223b000b5eba0/server.jar");
		versions.put("1.11","https://launcher.mojang.com/mc/game/1.11.1/server/1f97bd101e508d7b52b3d6a7879223b000b5eba0/server.jar");
		versions.put("1.10.2","https://launcher.mojang.com/mc/game/1.10.2/server/3d501b23df53c548254f5e3f66492d178a48db63/server.jar");
		versions.put("1.10.1","https://launcher.mojang.com/mc/game/1.10.1/server/cb4c6f9f51a845b09a8861cdbe0eea3ff6996dee/server.jar");
		versions.put("1.10","https://launcher.mojang.com/mc/game/1.10/server/a96617ffdf5dabbb718ab11a9a68e50545fc5bee/server.jar");
		versions.put("1.9.4","https://launcher.mojang.com/mc/game/1.9.4/server/edbb7b1758af33d365bf835eb9d13de005b1e274/server.jar");
		versions.put("1.9.3","https://launcher.mojang.com/mc/game/1.9.3/server/8e897b6b6d784f745332644f4d104f7a6e737ccf/server.jar");
		versions.put("1.9.2","https://launcher.mojang.com/mc/game/1.9.2/server/2b95cc7b136017e064c46d04a5825fe4cfa1be30/server.jar");
		versions.put("1.9.1","https://launcher.mojang.com/mc/game/1.9.1/server/bf95d9118d9b4b827f524c878efd275125b56181/server.jar");
		versions.put("1.9","https://launcher.mojang.com/mc/game/1.9/server/b4d449cf2918e0f3bd8aa18954b916a4d1880f0d/server.jar");
		versions.put("1.8.9","https://launcher.mojang.com/mc/game/1.8.9/server/b58b2ceb36e01bcd8dbf49c8fb66c55a9f0676cd/server.jar");
		versions.put("1.8.8","https://launcher.mojang.com/mc/game/1.8.8/server/5fafba3f58c40dc51b5c3ca72a98f62dfdae1db7/server.jar");
		versions.put("1.8.7","https://launcher.mojang.com/mc/game/1.8.7/server/35c59e16d1f3b751cd20b76b9b8a19045de363a9/server.jar");
		versions.put("1.8.6","https://launcher.mojang.com/mc/game/1.8.6/server/2bd44b53198f143fb278f8bec3a505dad0beacd2/server.jar");
		versions.put("1.8.5","https://launcher.mojang.com/mc/game/1.8.5/server/ea6dd23658b167dbc0877015d1072cac21ab6eee/server.jar");
		versions.put("1.8.4","https://launcher.mojang.com/mc/game/1.8.4/server/dd4b5eba1c79500390e0b0f45162fa70d38f8a3d/server.jar");
		versions.put("1.8.3","https://launcher.mojang.com/mc/game/1.8.3/server/163ba351cb86f6390450bb2a67fafeb92b6c0f2f/server.jar");
		versions.put("1.8.2","https://launcher.mojang.com/mc/game/1.8.2/server/a37bdd5210137354ed1bfe3dac0a5b77fe08fe2e/server.jar");
		versions.put("1.8.1","https://launcher.mojang.com/mc/game/1.8.1/server/68bfb524888f7c0ab939025e07e5de08843dac0f/server.jar");
		versions.put("1.8","https://launcher.mojang.com/mc/game/1.8/server/a028f00e678ee5c6aef0e29656dca091b5df11c7/server.jar");
		versions.put("1.7.10","https://launcher.mojang.com/mc/game/1.7.10/server/952438ac4e01b4d115c5fc38f891710c4941df29/server.jar");
		versions.put("1.7.9","https://launcher.mojang.com/mc/game/1.7.9/server/4cec86a928ec171fdc0c6b40de2de102f21601b5/server.jar");
		versions.put("1.7.8","https://launcher.mojang.com/mc/game/1.7.8/server/c69ebfb84c2577661770371c4accdd5f87b8b21d/server.jar");
		versions.put("1.7.7","https://launcher.mojang.com/mc/game/1.7.7/server/a6ffc1624da980986c6cc12a1ddc79ab1b025c62/server.jar");
		versions.put("1.7.6","https://launcher.mojang.com/mc/game/1.7.6/server/41ea7757d4d7f74b95fc1ac20f919a8e521e910c/server.jar");
		versions.put("1.7.5","https://launcher.mojang.com/mc/game/1.7.5/server/e1d557b2e31ea881404e41b05ec15c810415e060/server.jar");
		versions.put("1.7.4","https://launcher.mojang.com/mc/game/1.7.4/server/61220311cef80aecc4cd8afecd5f18ca6b9461ff/server.jar");
		versions.put("1.7.3","https://launcher.mojang.com/mc/game/1.7.3/server/707857a7bc7bf54fe60d557cca71004c34aa07bb/server.jar");
		versions.put("1.7.2","https://launcher.mojang.com/mc/game/1.7.2/server/3716cac82982e7c2eb09f83028b555e9ea606002/server.jar");
		
		port(8080);
		Spark.exception(Exception.class, (exception, request, response) -> {
			exception.printStackTrace();
		});

		TrustManager[] trustAllCerts = new TrustManager[] { new X509TrustManager() {

			public java.security.cert.X509Certificate[] getAcceptedIssuers() {
				return null;
			}

			public void checkClientTrusted(java.security.cert.X509Certificate[] certs, String authType) {
				// No need to implement.
			}

			public void checkServerTrusted(java.security.cert.X509Certificate[] certs, String authType) {
				// No need to implement.
			}
		} };

		// Install the all-trusting trust manager
		try {
			SSLContext sc = SSLContext.getInstance("SSL");
			sc.init(null, trustAllCerts, new java.security.SecureRandom());
			HttpsURLConnection.setDefaultSSLSocketFactory(sc.getSocketFactory());
		} catch (Exception e) {
			System.out.println(e);
		}

		get("/versions/waterfall", (request, response) -> {
			String json = callURL("https://yivesmirror.com/api/waterfall");
			JSONArray responsee = new JSONArray();
			JSONArray arrayy = new JSONArray(json);
			// System.out.println(arrayy.toString());
			for (int i = 0; i < arrayy.length(); i++) {
				JSONObject obj = arrayy.getJSONObject(i);
				Iterator key = obj.keys();
				while (key.hasNext()) {
					String k = key.next().toString();
					if (!k.endsWith(".jar") || k.contains("shaded") || k.contains("api")
							|| k.equals("latest"))
						continue;
					// System.out.println(obj);
					// JSONArray obj2 = new JSONArray(obj.getString("version"));
					//// Iterator key2 = obj2.keys();
					// System.out.println(key2.next());
					// System.out.println(obj);
					// System.out.println("Version: " + k);
					// JSONObject obj2 = new JSONObject(obj.get(k));
					// System.out.println("should be " + obj.get(k));
					JSONObject realObj = obj.getJSONObject(k);
					JSONObject urls = realObj.getJSONObject("urls");
					JSONObject version = realObj.getJSONObject("version");

					JSONObject temp = new JSONObject();
					temp.put("name", realObj.getString("name") + " [" + version.getString("minecraft") + "]");
					temp.put("url", urls.getString("free"));
					responsee.put(temp);
					// System.out.println(obj.get(k));
				}
			}

			return responsee.toString();
		});
		
		get("/versions/bungeecord", (request, response) -> {
			String json = callURL("https://yivesmirror.com/api/bungeecord");
			JSONArray responsee = new JSONArray();
			JSONArray arrayy = new JSONArray(json);
			// System.out.println(arrayy.toString());
			for (int i = 0; i < arrayy.length(); i++) {
				JSONObject obj = arrayy.getJSONObject(i);
				Iterator key = obj.keys();
				while (key.hasNext()) {
					String k = key.next().toString();
					if (!k.endsWith(".jar") || k.contains("shaded") || k.contains("api")
							|| k.equals("latest"))
						continue;
					// System.out.println(obj);
					// JSONArray obj2 = new JSONArray(obj.getString("version"));
					//// Iterator key2 = obj2.keys();
					// System.out.println(key2.next());
					// System.out.println(obj);
					// System.out.println("Version: " + k);
					// JSONObject obj2 = new JSONObject(obj.get(k));
					// System.out.println("should be " + obj.get(k));
					JSONObject realObj = obj.getJSONObject(k);
					JSONObject urls = realObj.getJSONObject("urls");
					JSONObject version = realObj.getJSONObject("version");

					JSONObject temp = new JSONObject();
					temp.put("name", realObj.getString("name") + " [" + version.getString("minecraft") + "]");
					temp.put("url", urls.getString("free"));
					responsee.put(temp);
					// System.out.println(obj.get(k));
				}
			}

			return responsee.toString();
		});
		
		get("/versions/spigot", (request, response) -> {
			String json = callURL("https://yivesmirror.com/api/spigot");
			JSONArray responsee = new JSONArray();
			JSONArray arrayy = new JSONArray(json);
			// System.out.println(arrayy.toString());
			for (int i = 0; i < arrayy.length(); i++) {
				JSONObject obj = arrayy.getJSONObject(i);
				Iterator key = obj.keys();
				while (key.hasNext()) {
					String k = key.next().toString();
					if (!k.endsWith(".jar") || k.contains("shaded-") || k.contains("spigot-api")
							|| k.equals("spigot-latest.jar"))
						continue;
					// System.out.println(obj);
					// JSONArray obj2 = new JSONArray(obj.getString("version"));
					//// Iterator key2 = obj2.keys();
					// System.out.println(key2.next());
					// System.out.println(obj);
					// System.out.println("Version: " + k);
					// JSONObject obj2 = new JSONObject(obj.get(k));
					// System.out.println("should be " + obj.get(k));
					JSONObject realObj = obj.getJSONObject(k);
					JSONObject urls = realObj.getJSONObject("urls");
					JSONObject version = realObj.getJSONObject("version");

					JSONObject temp = new JSONObject();
					temp.put("name", realObj.getString("name") + " [" + version.getString("minecraft") + "]");
					temp.put("url", urls.getString("free"));
					responsee.put(temp);
					// System.out.println(obj.get(k));
				}
			}

			return responsee.toString();
		});

		get("/versions/bukkit", (request, response) -> {
			String json = callURL("https://yivesmirror.com/api/bukkit");
			JSONArray responsee = new JSONArray();
			JSONArray arrayy = new JSONArray(json);
			// System.out.println(arrayy.toString());
			for (int i = 0; i < arrayy.length(); i++) {
				JSONObject obj = arrayy.getJSONObject(i);
				Iterator key = obj.keys();
				while (key.hasNext()) {
					String k = key.next().toString();
					if (!k.endsWith(".jar") || k.contains("shaded") || k.contains("latest"))
						continue;
					// System.out.println(obj);
					// JSONArray obj2 = new JSONArray(obj.getString("version"));
					//// Iterator key2 = obj2.keys();
					// System.out.println(key2.next());
					// System.out.println(obj);
					// System.out.println("Version: " + k);
					// JSONObject obj2 = new JSONObject(obj.get(k));
					// System.out.println("should be " + obj.get(k));
					JSONObject realObj = obj.getJSONObject(k);
					JSONObject urls = realObj.getJSONObject("urls");
					JSONObject version = realObj.getJSONObject("version");

					JSONObject temp = new JSONObject();
					temp.put("name", realObj.getString("name") + " [" + version.getString("minecraft") + "]");
					temp.put("url", urls.getString("free"));
					responsee.put(temp);
					// System.out.println(obj.get(k));
				}
			}

			return responsee.toString();
		});

		get("/versions/vanilla", (request, response) -> {
			JSONArray responsee = new JSONArray();
			
			for(String name : versions.keySet()){
				JSONObject temp = new JSONObject();
				temp.put("name", name);
				temp.put("url", versions.get(name));
				responsee.put(temp);
			}
			return responsee.toString();
		});

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
			throw new RuntimeException("Exception while calling URL:" + myURL, e);
		}

		return sb.toString();
	}
}
