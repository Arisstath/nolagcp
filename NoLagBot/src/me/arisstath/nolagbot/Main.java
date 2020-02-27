package me.arisstath.nolagbot;

import java.io.File;
import java.io.IOException;
import java.security.KeyStoreException;
import java.security.NoSuchAlgorithmException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashMap;
import java.util.Scanner;

import javax.net.ssl.HttpsURLConnection;
import javax.net.ssl.SSLContext;
import javax.net.ssl.TrustManager;
import javax.net.ssl.X509TrustManager;
import javax.security.auth.login.LoginException;

import org.jsoup.Connection;
import org.jsoup.Jsoup;

import com.google.code.chatterbotapi.ChatterBotFactory;
import com.google.code.chatterbotapi.ChatterBotSession;
import com.google.code.chatterbotapi.ChatterBotType;

import net.dv8tion.jda.core.AccountType;
import net.dv8tion.jda.core.JDA;
import net.dv8tion.jda.core.JDABuilder;
import net.dv8tion.jda.core.MessageBuilder;
import net.dv8tion.jda.core.entities.Message;
import net.dv8tion.jda.core.entities.Role;
import net.dv8tion.jda.core.events.message.MessageReceivedEvent;
import net.dv8tion.jda.core.exceptions.RateLimitedException;
import net.dv8tion.jda.core.hooks.ListenerAdapter;

public class Main extends ListenerAdapter {
	static int largestWordLength = 0;
	public static HashMap<String, String[]> words = new HashMap<>();
	public static ChatterBotSession bot1session;
	public static Database db;

	public static long isUp(String url) {
		long start = System.currentTimeMillis();
		Connection connection = Jsoup.connect(url);
		connection.ignoreHttpErrors(true);
		try {
			if (connection.get().toString().contains("404")) {
				return System.currentTimeMillis() - start;
			}
		} catch (IOException e) {
			e.printStackTrace();
			return 0;
		}
		return 0;
	}

	public static String get(String url) {
		Connection connection = Jsoup.connect(url);
		connection.ignoreHttpErrors(true);
		try {
			return connection.get() + "";
		} catch (IOException e) {
			e.printStackTrace();
			return "";
		}
	}

	public static void loadConfigs() {
		try {
			Scanner scanner = new Scanner(new File("swears.csv"));

			String line = "";
			int counter = 0;
			while (scanner.hasNextLine()) {
				line = scanner.nextLine();
				counter++;
				String[] content = null;
				try {
					content = line.split(",");
					if (content.length == 0) {
						continue;
					}
					String word = content[0];
					String[] ignore_in_combination_with_words = new String[] {};
					if (content.length > 1) {
						ignore_in_combination_with_words = content[1].split("_");
					}

					if (word.length() > largestWordLength) {
						largestWordLength = word.length();
					}
					words.put(word.replaceAll(" ", ""), ignore_in_combination_with_words);

				} catch (Exception e) {
					e.printStackTrace();
				}

			}
			scanner.close();
			System.out.println("Loaded " + counter + " words to filter out");
		} catch (IOException e) {
			e.printStackTrace();
		}

	}

	public static ArrayList<String> badWordsFound(String input) {
		if (input == null) {
			return new ArrayList<>();
		}

		// remove leetspeak
		input = input.replaceAll("1", "i");
		input = input.replaceAll("!", "i");
		input = input.replaceAll("3", "e");
		input = input.replaceAll("4", "a");
		input = input.replaceAll("@", "a");
		input = input.replaceAll("5", "s");
		input = input.replaceAll("7", "t");
		input = input.replaceAll("0", "o");
		input = input.replaceAll("9", "g");

		input = input.replaceAll("ã", "a");
		input = input.replaceAll("ü", "u");
		input = input.replaceAll("ú", "u");
		input = input.replaceAll("ù", "u");
		ArrayList<String> badWords = new ArrayList<>();
		input = input.toLowerCase().replaceAll("[^a-zA-Z]", "");

		// iterate over each letter in the word
		for (int start = 0; start < input.length(); start++) {
			// from each letter, keep going to find bad words until either the
			// end of the sentence is reached, or the max word length is
			// reached.
			for (int offset = 1; offset < (input.length() + 1 - start) && offset < largestWordLength; offset++) {
				String wordToCheck = input.substring(start, start + offset);
				if (words.containsKey(wordToCheck)) {
					// for example, if you want to say the word bass, that
					// should be possible.
					String[] ignoreCheck = words.get(wordToCheck);
					boolean ignore = false;
					for (int s = 0; s < ignoreCheck.length; s++) {
						if (input.contains(ignoreCheck[s])) {
							ignore = true;
							break;
						}
					}
					if (!ignore) {
						badWords.add(wordToCheck);
					}
				}
			}
		}

		return badWords;

	}

	public static void main(String[] args) throws LoginException, RateLimitedException, InterruptedException,
			NoSuchAlgorithmException, KeyStoreException {
		loadConfigs();
		db = new Database("217.182.72.100", "cpweb", "pT4MxRFKxTyqgcbC", "nolagcp");
		JDA jda = new JDABuilder(AccountType.BOT)
				.setToken("MzQxNTI1ODY1NjUxMzcyMDM0.DGCWQw.PZOj6d9kSgkX8y358cOkWf2Bwko").buildBlocking();
		jda.addEventListener(new Main());
		ChatterBotFactory factory = new ChatterBotFactory();

		try {
			bot1session = factory.create(ChatterBotType.PANDORABOTS, "b0dafd24ee35a477").createSession();
		} catch (Exception e) {
			e.printStackTrace();
		}
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
	}

	@Override
	public void onMessageReceived(MessageReceivedEvent event) {
		if (event.getAuthor().isBot()) {
			return;
		}
		if (event.getMessage().getContent().startsWith("#link ")) {
			System.out.println(event.getMessage().getContent());
			String message = event.getMessage().getContent().replace("#link ", "");
			// Check if user is already linked
			for (Role role : event.getMember().getRoles()) {
				if (role.getName().equals("Member")) {
					event.getChannel().sendMessage(event.getAuthor().getAsMention() + ", you are already a member.")
							.queue();
					return;
				}
			}
			String username = db.fetchUsernameFromPin(message);
			if (username == null) {
				event.getChannel().sendMessage(event.getAuthor().getAsMention() + ", this is not a valid token.")
						.queue();
				return;
			}
			String linkedDiscord = db.getLinkedDiscord(message);
			if (linkedDiscord != null && !linkedDiscord.isEmpty()) {
				event.getChannel()
						.sendMessage(event.getAuthor().getAsMention() + ", this NoLagCP account is already linked.")
						.queue();
				return;
			}
			Role memberRole = null;
			for (Role role : event.getMember().getGuild().getRoles()) {
				if (role.getName().equals("Member")) {
					memberRole = role;
					break;
				}
			}
			db.updateDiscord(username, event.getAuthor());
			event.getGuild().getController().addRolesToMember(event.getMember(), memberRole).queue();
			event.getGuild().getController().setNickname(event.getMember(), username).queue();
			event.getChannel().sendMessage(event.getAuthor().getAsMention() + ", the NoLagCP account *" + username
					+ "* has been connected with your Discord account.").queue();
		}
		if (event.getMessage().getContent().startsWith("#advertise")) {
			System.out.println(event.getMessage().getContent());
			String[] args = event.getMessage().getContent().split(" ");
			if (args.length < 3) {
				event.getChannel()
						.sendMessage(
								"Incorrect usage. Correct usage is #advertise <Server ID> <Info about your server>")
						.queue();
				return;
			}
			boolean member = false;
			for (Role role : event.getMember().getGuild().getRoles()) {
				if (role.getName().equals("Member")) {
					member = true;
					break;
				}
			}
			if (!member) {
				event.getChannel().sendMessage("You need to link first your Discord account with your NoLagCP account.")
						.queue();
				return;
			}
			try {
				Integer.parseInt(args[1]);
			} catch (Exception ex) {
				event.getChannel().sendMessage("This is not a valid server ID.").queue();
				return;
			}
			int serverID = Integer.parseInt(args[1]);
			String hostandport = db.getNoLagServer(event.getMember().getNickname(), serverID);
			if (hostandport == null || hostandport.isEmpty()) {
				event.getChannel().sendMessage("The server with ID #" + serverID + " is not owned by your account.")
						.queue();
				return;
			}
			String[] desc = Arrays.copyOfRange(args, 2, args.length);
			String description = "";
			for (String des : desc) {
				description += des + " ";
			}
			event.getGuild().getTextChannelById("349508678480560129")
					.sendMessage("===================================\n**Advertisement** *by "
							+ event.getAuthor().getName() + "#" + event.getAuthor().getDiscriminator()
							+ "*\n\n**Server IP**: " + hostandport + "\n\n**Description**:```" + description + "```")
					.queue();
			return;
		}
		if (event.getMessage().getContent().startsWith("#suggest ")) {
			System.out.println(event.getMessage().getContent());
			String message = event.getMessage().getContent().replace("#suggest ", "");
			event.getGuild().getTextChannelById("343364748219514890")
					.sendMessage("**Suggestion** *by " + event.getAuthor().getName() + "#"
							+ event.getAuthor().getDiscriminator() + "*\n```" + message + "```")
					.queue((msg) -> {
						msg.addReaction("✔").queue((x) -> {
							msg.addReaction("❌").queue();
						});
					});

		}
		// some cmds
		if (event.getMessage().getContent().startsWith("#announce ")) {
			System.out.println(event.getMessage().getContent());
			if (!event.getMessage().getAuthor().getId().equals("320917419608768512")
					&& !event.getMessage().getAuthor().getId().equals("213665624977571840")) {
				System.out.println("denied");
				return;
			}
			String message = event.getMessage().getContent().replace("#announce ", "");
			String online = get("https://live.mcsrv.top:3000/stats");
			get("https://live.mcsrv.top:3000/?secret=lIKWvkTewDg4baFBgMAY&message=" + message);
			event.getChannel().sendMessage("Message has been sent to " + online + " users.").queue();
		}
		if (event.getMessage().getContent().startsWith("#eval ")) {
			System.out.println(event.getMessage().getContent());
			if (!event.getMessage().getAuthor().getId().equals("320917419608768512")
					&& !event.getMessage().getAuthor().getId().equals("213665624977571840")) {
				System.out.println("denied");
				return;
			}
			String message = event.getMessage().getContent().replace("#eval ", "");
			String online = get("https://live.mcsrv.top:3000/stats");
			get("https://live.mcsrv.top:3000/eval?secret=lIKWvkTewDg4baFBgMAY&message=" + message);
			event.getChannel().sendMessage("Message has been sent to " + online + " users.").queue();
		}
		// swearings
		/*
		 * if (badWordsFound(event.getMessage().getContent()).size() > 0) {
		 * event.getMessage().delete().queue();
		 * event.getChannel().sendMessage(event.getMessage().getAuthor().
		 * getAsMention() + " Please refrain from swearing.").queue(); return; }
		 */
		if (event.getChannel().getId().equals("310073050936639499")) {
			// suport channel
			String msg = event.getMessage().getContent().toLowerCase();
			if (msg.contains("failed to bind to port")) {
				event.getChannel()
						.sendMessage(event.getMessage().getAuthor().getAsMention()
								+ " It seems that there are 2 instances of your Minecraft server, please click on the Stop/Start button to kill the other instance.")
						.queue();
				return;
			}
			if (msg.contains("ftp")) {
				// handle cases
				if (msg.contains("authentication failed") || msg.contains("login incorrect")) {
					event.getChannel()
							.sendMessage(event.getMessage().getAuthor().getAsMention()
									+ " It seems that the credentials are wrong, please make sure that you use the correct username for the specific server and that you did not accidentally copied a blank space with your password.")
							.queue();
					return;
				}
				if (msg.contains("time") || msg.contains("working") || msg.contains("")) {
					event.getChannel()
							.sendMessage(event.getMessage().getAuthor().getAsMention()
									+ " Please make sure that:\n- You use 1234 as port\n- You don't use your NoLagCP username, there is a different username for each server\n- Make sure that you have selected FTP and not SFTP.\nIf the problem persists you can type !status on #bot-chat to check if any node is down.")
							.queue();
					return;
				}
			}
			if (msg.contains("please wait") || msg.contains("node offline")) {
				event.getChannel()
						.sendMessage(event.getMessage().getAuthor().getAsMention()
								+ " Please type !status on #bot-chat and check if any node is offline. If it is mention a staff member as soon as possible. Sometimes you need to wait a bit.")
						.queue();
				return;
			}
			if (msg.contains("cant") && msg.contains("connect")) {
				event.getChannel().sendMessage(event.getMessage().getAuthor().getAsMention()
						+ " Please make sure that you use the correct port").queue();
				return;
			}
			if (msg.contains("cant")) {
				if (msg.contains("connect") || msg.contains("join") || msg.contains(""))
					event.getChannel()
							.sendMessage(event.getMessage().getAuthor().getAsMention()
									+ " It seems that one of your plugins is causing issues, please check your plugins.")
							.queue();
				return;
			}
			if (msg.contains("locat")) {
				event.getChannel().sendMessage(
						event.getMessage().getAuthor().getAsMention() + " Our servers are located in Germany & USA.")
						.queue();
				return;
			}
			if (msg.contains("price") || msg.contains("pricing")) {
				event.getChannel().sendMessage(event.getMessage().getAuthor().getAsMention()
						+ " You can see our plans at https://www.nolag.host/minecraft.php").queue();
				return;
			}
			if (msg.contains("registered") && (msg.contains("business") || msg.contains("company"))) {
				MessageBuilder b = new MessageBuilder();
				b.append(event.getMessage().getAuthor().getAsMention()
						+ " Yes, we are a registered business. You can check this by our Green Bar SSL.");
				Message built = b.build();
				try {
					event.getChannel().sendFile(new File("ssl.png"), built).queue();
				} catch (IOException e) {
				}
				return;
			}
			if (msg.contains("are you ") && (msg.contains("lesbian") || msg.contains("gay") || msg.contains("straight")
					|| msg.contains("bi") || msg.contains("trans"))) {
				event.getChannel().sendMessage(event.getMessage().getAuthor().getAsMention()
						+ " You silly! Bots do not have genders :frowning:").queue();
				return;
			}
			try {
				String thought = bot1session.think(event.getMessage().getStrippedContent());
				event.getChannel().sendMessage(event.getAuthor().getAsMention() + " " + thought).queue();
			} catch (Exception e) {
				e.printStackTrace();
			}
		}

		if (event.getGuild() == null || !event.getGuild().getId().equals("295274094562246656")) {
			return;
		}

		if (event.getMessage().getContent().equalsIgnoreCase("!status")) {
			System.out.println("fetching status...");
			event.getChannel().sendTyping().queue();
			String message = "*NoLag Status Bot*\n";
			message += "\nNuremberg Location:\n";
			for (int i = 1; i <= 2; i++) {
				message += "**DE0" + i + "**: ";
				long responseTime = isUp("https://de0" + i + ".mcsrv.top");
				if (responseTime != 0) {
					message += ":white_check_mark: (" + responseTime + "ms)";
				} else {
					message += ":negative_squared_cross_mark:";
				}
				message += "\n\n";
			}
			message += "\nDallas Location:\n";
			for (int i = 2; i <= 3; i++) {
				message += "**DA0" + i + "**: ";
				long responseTime = isUp("https://da0" + i + ".mcsrv.top");
				if (responseTime != 0) {
					message += ":white_check_mark: (" + responseTime + "ms)";
				} else {
					message += ":negative_squared_cross_mark:";
				}
				message += "\n\n";
			}
			message += "\n";
			message += "\nLos Angeles Location:\n";
			for (int i = 1; i <= 2; i++) {
				message += "**US" + i + "**: ";
				long responseTime = isUp("https://us" + i + ".mcsrv.top");
				if (responseTime != 0) {
					message += ":white_check_mark: (" + responseTime + "ms)";
				} else {
					message += ":negative_squared_cross_mark:";
				}
				message += "\n\n";
			}
			message += "\n";
			message += "\nOld European Nodes:\n";
			for (int i = 6; i <= 9; i++) {
				message += "**EU" + i + "**: ";
				long responseTime = isUp("https://eu" + i + ".mcsrv.top");
				if (responseTime != 0) {
					message += ":white_check_mark: (" + responseTime + "ms)";
				} else {
					message += ":negative_squared_cross_mark:";
				}
				message += "\n\n";
			}
			event.getChannel().sendMessage(message).queue();
		}
	}
}
