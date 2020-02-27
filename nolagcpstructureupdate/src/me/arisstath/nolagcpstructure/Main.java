package me.arisstath.nolagcpstructure;

import java.io.File;
import java.io.PrintWriter;

public class Main {

	public static void main(String[] args) throws Exception{
		System.out.println("NoLagCP Remapper - Pray for it to work(TM)");
		int cnt = 0;
		PrintWriter writer = new PrintWriter("/root/nolagcpstructure.sh");
		for (File file : new File("/home").listFiles()) {
			if(!file.isDirectory()){
				continue;
			}
			if(file.getName().equalsIgnoreCase("XenoPanelMigration")){
				continue;
			}
			if(file.getName().equalsIgnoreCase("NoLag01")){
				continue;
			}
			if(file.getName().equalsIgnoreCase("XenoPanel")){
				continue;
			}
			for (File serverFolder : file.listFiles()) {
				if (!serverFolder.isDirectory()) {
					continue;
				}
				try{
					Integer.parseInt(serverFolder.getName());
				} catch (Exception ex){
					continue;
				}
				String originalDirectory = "/home/" + file.getName() + "/" + serverFolder.getName();
				String newDirectory = "/home/" + "mc_" + serverFolder.getName();
				System.out.println(originalDirectory + " -> " + newDirectory);
				writer.println("mv " + originalDirectory + " " + newDirectory);
				continue; //git out of the dir
			}
		}
		writer.close();
	}
}
