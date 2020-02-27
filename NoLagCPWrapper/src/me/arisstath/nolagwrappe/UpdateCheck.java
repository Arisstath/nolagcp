package me.arisstath.nolagwrappe;

import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;

public class UpdateCheck extends Thread{
	
	@Override
	public void run(){
		while (true) {
			Main.update();
			try {
				Thread.sleep(1000 * 30);
			} catch (InterruptedException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
	}
}
