package me.arisstath.cp.utils;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.lang.reflect.Field;
import java.util.ArrayList;
import java.util.concurrent.TimeUnit;
import java.util.logging.Level;
import java.util.logging.Logger;

import org.apache.commons.exec.CommandLine;
import org.apache.commons.exec.DefaultExecutor;
import org.apache.commons.exec.ExecuteWatchdog;
import org.apache.commons.exec.PumpStreamHandler;
import org.apache.ftpserver.util.OS;

import com.sun.jna.Library;
import com.sun.jna.Native;
import com.sun.jna.Platform;

import me.arisstath.cp.Main;
import me.arisstath.cp.Main.LogType;

//f
public class RuntimeUtils {

	private interface Kernel32 extends Library { // f

		public static Kernel32 INSTANCE = (Kernel32) Native.loadLibrary("kernel32", Kernel32.class);

		public int GetProcessId(Long hProcess);
	}

	public static boolean linuxUserExists(String username) {
		try {
			Process p = Runtime.getRuntime().exec("getent passwd");
			BufferedReader reader = new BufferedReader(new InputStreamReader(p.getInputStream()));
			String line = null;
			while ((line = reader.readLine()) != null) {
				if (line.split(":")[0].equalsIgnoreCase(username)) {
					return true;
				}
			}
		} catch (Exception ex) {
			ex.printStackTrace();
		}
		return false;
	}

	private static void executeBash(String cmd) throws Exception {
		String[] cmdd = { "/bin/bash", "-c", cmd };
		Process pb = Runtime.getRuntime().exec(cmdd);

		String line;
		BufferedReader input = new BufferedReader(new InputStreamReader(pb.getInputStream()));
		while ((line = input.readLine()) != null) {
			Main.log(line, LogType.DEBUG);
		}
		input.close();
	}

	public static void killServer(int serverid) {
		int serverPID = getScreenServerPID(serverid);
		if (serverPID == -1) {
			return;
		}
		try {
			executeBash("kill -9 " + serverPID);
		} catch (Exception e) {
			e.printStackTrace();
		}
	}

	public static ArrayList<String> runningScreens() {
		ArrayList<String> runningScreens = new ArrayList<String>();
		try {
			Process p = Runtime.getRuntime().exec("screen -ls");
			BufferedReader reader = new BufferedReader(new InputStreamReader(p.getInputStream()));
			String line = null;
			while ((line = reader.readLine()) != null) {
				line = line.trim();
				if (line.split("\\.").length > 1) {
					// System.out.println("Possible line: " + line);
					String screenName = line.split("\\.")[1];

					// TODO: Use regex
					screenName = screenName.replace("(Attached)", "");
					screenName = screenName.replace("(Detached)", "");
					screenName = screenName.replaceAll(" ", "");

					// System.out.println("Found screen: " + screenName);
					String[] screenEntry = screenName.split("_");
					if (screenEntry.length > 1) {
						if (screenEntry[0].startsWith("mc")) {
							screenEntry[1] = screenEntry[1].trim();
							runningScreens.add(line.split("\\.")[0]);
						}
					}
				}
			}
		} catch (Exception ex) {
			ex.printStackTrace();
		}
		return runningScreens;
	}

	public static int getScreenServerPID(int serverid) {
		try {
			Process p = Runtime.getRuntime().exec("screen -ls");
			BufferedReader reader = new BufferedReader(new InputStreamReader(p.getInputStream()));
			String line = null;
			while ((line = reader.readLine()) != null) {
				line = line.trim();
				if (line.split("\\.").length > 1) {
					// System.out.println("Possible line: " + line);
					String screenName = line.split("\\.")[1];

					// TODO: Use regex
					screenName = screenName.replace("(Attached)", "");
					screenName = screenName.replace("(Detached)", "");
					screenName = screenName.replaceAll(" ", "");

					// System.out.println("Found screen: " + screenName);
					String[] screenEntry = screenName.split("_");
					if (screenEntry.length > 1) {
						if (screenEntry[0].startsWith("mc")) {
							screenEntry[1] = screenEntry[1].trim();
							// System.out.println("Found screen for " +
							// screenEntry[1] + " server");
							if (screenEntry[1].replaceAll(" ", "").equalsIgnoreCase(serverid + "")) {
								return Integer.parseInt(line.split("\\.")[0]);
							}
						}
					}
				}
			}
		} catch (Exception ex) {
			ex.printStackTrace();
		}
		System.out.println("RETURNED -1");
		return -1;
	}

	public static boolean isProcessRunning(int pid, int timeout, TimeUnit timeunit) throws java.io.IOException {
		String line;
		if (OS.isFamilyWindows()) {
			// tasklist exit code is always 0. Parse output
			// findstr exit code 0 if found pid, 1 if it doesn't
			line = "cmd /c \"tasklist /FI \"PID eq " + pid + "\" | findstr " + pid + "\"";
		} else {
			// ps exit code 0 if process exists, 1 if it doesn't
			line = "ps -p " + pid;
			// `-p` is POSIX/BSD-compliant, `--pid`
			// isn't<ref>https://github.com/apache/storm/pull/296#discussion_r20535744</ref>
		}
		CommandLine cmdLine = CommandLine.parse(line);
		DefaultExecutor executor = new DefaultExecutor();
		// disable logging of stdout/strderr
		executor.setStreamHandler(new PumpStreamHandler(null, null, null));
		// disable exception for valid exit values
		executor.setExitValues(new int[] { 0, 1 });
		// set timer for zombie process
		ExecuteWatchdog timeoutWatchdog = new ExecuteWatchdog(timeunit.toMillis(timeout));
		executor.setWatchdog(timeoutWatchdog);
		int exitValue = executor.execute(cmdLine);
		// 0 is the default exit code which means the process exists
		return exitValue == 0;
	}

	public static int getPidP(Process p) {
		Field f;
		if (Platform.isWindows()) {
			try {
				f = p.getClass().getDeclaredField("handle");
				f.setAccessible(true);
				int pid = Kernel32.INSTANCE.GetProcessId((Long) f.get(p));
				return pid;
			} catch (Exception ex) {
				Logger.getLogger(Main.class.getName()).log(Level.SEVERE, null, ex);
			}
		} else if (Platform.isLinux()) {
			try {
				f = p.getClass().getDeclaredField("pid");
				f.setAccessible(true);
				int pid = (Integer) f.get(p);
				return pid;
			} catch (Exception ex) {
				Logger.getLogger(Main.class.getName()).log(Level.SEVERE, null, ex);
			}
		} else {
		}
		return 0;
	}

}
