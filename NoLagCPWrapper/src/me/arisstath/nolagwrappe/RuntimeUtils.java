package me.arisstath.nolagwrappe;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.lang.reflect.Field;
import java.util.logging.Level;
import java.util.logging.Logger;

import com.sun.jna.Library;
import com.sun.jna.Native;
import com.sun.jna.Platform;
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
