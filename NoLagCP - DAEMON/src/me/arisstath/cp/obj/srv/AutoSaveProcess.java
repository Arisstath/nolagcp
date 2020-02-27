package me.arisstath.cp.obj.srv;

import me.arisstath.cp.Main;
import me.arisstath.cp.Main.LogType;
import me.arisstath.cp.obj.srv.MinecraftServer.ServerStatus;

public class AutoSaveProcess extends Thread {

	public AutoSaveProcess() {
	}

	@Override
	public void run() {
		while (true) {
			for (MinecraftServer mcsrv : Main.getServersList().toArray()) {
				if (mcsrv.getStatus() == ServerStatus.STARTED) {
					if (mcsrv.autoSave)
						mcsrv.sendCommand("save-all");
				}
				try {
					sleep(1000 * 60 * 5);
				} catch (InterruptedException e) {
					e.printStackTrace();
				}
			}
		}
	}

}
