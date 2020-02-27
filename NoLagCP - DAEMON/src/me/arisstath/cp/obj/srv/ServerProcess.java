package me.arisstath.cp.obj.srv;

import java.util.ArrayList;

import me.arisstath.cp.Main;
import me.arisstath.cp.Main.LogType;
import me.arisstath.cp.obj.srv.MinecraftServer.ServerStatus;
import me.arisstath.cp.utils.RuntimeUtils;

public class ServerProcess extends Thread {

	public ServerProcess() {
		Main.log("Started server process monitor", LogType.DEBUG);
	}

	@Override
	public void run() {
		while (true) {
			ArrayList<String> runningServers = RuntimeUtils.runningScreens();
			for (MinecraftServer mcsrv : Main.getServersList().toArray()) {
				if (!runningServers.contains(mcsrv.ID)) {
					mcsrv.setStatus(ServerStatus.STOPPED);
				} else {
					mcsrv.setStatus(ServerStatus.STARTED);
					mcsrv.setPid(RuntimeUtils.getScreenServerPID(mcsrv.getPid()));
				}
				try {
					sleep(30 * 1000);
				} catch (InterruptedException e) {
					e.printStackTrace();
				}
			}
		}
	}

}
