package me.arisstath.nodetransfe;

public class TransferJob {
	int id;
	String targetIP;
	String targetNode;
	int targetServer;

	public TransferJob(int id, String targetIP, String targetNode, int targetServer) {
		this.id = id;
		this.targetIP = targetIP;
		this.targetNode = targetNode;
		this.targetServer = targetServer;
	}

	public String getTargetIP() {
		return targetIP;
	}

	public void setTargetIP(String targetIP) {
		this.targetIP = targetIP;
	}

	public String getTargetNode() {
		return targetNode;
	}

	public void setTargetNode(String targetNode) {
		this.targetNode = targetNode;
	}

	public int getTargetServer() {
		return targetServer;
	}

	public void setTargetServer(int targetServer) {
		this.targetServer = targetServer;
	}

}
