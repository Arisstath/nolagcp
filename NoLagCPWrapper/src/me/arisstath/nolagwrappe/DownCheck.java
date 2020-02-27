package me.arisstath.nolagwrappe;

import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;

public class DownCheck extends Thread{
	
	@Override
	public void run(){
		while (true) {
			Main.isDown();
			try {
				Thread.sleep(1000 * 30);
			} catch (InterruptedException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
	}
}
