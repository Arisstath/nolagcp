package me.arisstath.cp.managers.ftp;

import java.io.IOException;

import org.apache.ftpserver.ftplet.DefaultFtpReply;
import org.apache.ftpserver.ftplet.DefaultFtplet;
import org.apache.ftpserver.ftplet.FtpException;
import org.apache.ftpserver.ftplet.FtpReply;
import org.apache.ftpserver.ftplet.FtpRequest;
import org.apache.ftpserver.ftplet.FtpSession;
import org.apache.ftpserver.ftplet.Ftplet;
import org.apache.ftpserver.ftplet.FtpletResult;

import me.arisstath.cp.Main;
import me.arisstath.cp.Main.LogType;

public class NoLagFtplet extends DefaultFtplet {


	@Override
	public FtpletResult onLogin(FtpSession session, FtpRequest request) throws FtpException, IOException {
		// System.out.println(session.getUser().getName() + " Logged in");
		Main.log("\"" + session.getUser().getName() + "\"" + " has logged in.", LogType.DEBUG);
		//FtpReply rep = new DefaultFtpReply(FtpReply.REPLY_220_SERVICE_READY, "Welcome to NoLag.host control panel.\nPanel is in alpha phase, report any bugs!");
		//session.write(rep);
		
		return super.onLogin(session, request);
	}

	@Override
	public FtpletResult onConnect(FtpSession session) throws FtpException, IOException{
		//FtpReply rep = new DefaultFtpReply(FtpReply.REPLY_220_SERVICE_READY, "-==========================\nWelcome to NoLagCP\nReport any bugs to our Discord server.\nNoLagCP is powered by Java\n==========================");
	//	session.write(rep);
		return super.onConnect(session);
	}
	@Override
	public FtpletResult onDisconnect(FtpSession session) throws FtpException, IOException {
		// System.out.println(session.getUser().getName() + " Disconnected");
		return super.onDisconnect(session);
	}

	@Override
	public FtpletResult onRenameStart(FtpSession session, FtpRequest request) throws FtpException, IOException {
		if(request.getArgument().toLowerCase().contains("nolagagent.jar")){
			FtpReply rep = new DefaultFtpReply(FtpReply.REPLY_451_REQUESTED_ACTION_ABORTED, "NoLagAgent is copyrighted, you are not allowed to download it.");
			session.write(rep);
			return FtpletResult.DISCONNECT;
		}
		// System.out.println(session.getUser().getName() + " Started
		// Downloading File " + request.getArgument());
		//FtpReply rep = new DefaultFtpReply(FtpReply.REPLY_220_SERVICE_READY, "Welcome to NoLag.host control panel.\nPanel is in alpha phase, report any bugs!");
		//session.write(rep);
		
		return super.onDownloadStart(session, request);
	}
	
	@Override
	public FtpletResult onDownloadStart(FtpSession session, FtpRequest request) throws FtpException, IOException {
		if(request.getArgument().toLowerCase().contains("nolagagent.jar")){
			FtpReply rep = new DefaultFtpReply(FtpReply.REPLY_451_REQUESTED_ACTION_ABORTED, "NoLagAgent is copyrighted, you are not allowed to download it.");
			session.write(rep);
			return FtpletResult.DISCONNECT;
		}
		// System.out.println(session.getUser().getName() + " Started
		// Downloading File " + request.getArgument());
		//FtpReply rep = new DefaultFtpReply(FtpReply.REPLY_220_SERVICE_READY, "Welcome to NoLag.host control panel.\nPanel is in alpha phase, report any bugs!");
		//session.write(rep);
		
		return super.onDownloadStart(session, request);
	}

	@Override
	public FtpletResult onDownloadEnd(FtpSession session, FtpRequest request) throws FtpException, IOException {
		// System.out.println("Finished Downloading " + request.getArgument());
		return super.onDownloadEnd(session, request);
	}
}
