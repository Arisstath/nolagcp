package me.arisstath.nodetransfe;

public class MCServer {

	String directory;
	int id;
	
	
	public MCServer(String directory, int id) {
		super();
		this.directory = directory;
		this.id = id;
	}
	public String getDirectory() {
		return directory;
	}
	public void setDirectory(String directory) {
		this.directory = directory;
	}
	public int getId() {
		return id;
	}
	public void setId(int id) {
		this.id = id;
	}
	
	
}
