import java.io.*;
import javax.servlet.*;
import javax.servlet.http.*;
import java.net.*;
import org.json.*;


public class StockSearch extends HttpServlet {

	public void doGet(HttpServletRequest request, HttpServletResponse response) throws IOException, ServletException {
		String symbol = request.getParameter("symbol");
		String urlstr ="http://default-environment-dkjye6zrye.elasticbeanstalk.com/stockscript.php/";
		urlstr=urlstr + "?symbol=" + symbol;
		response.setContentType("text/JSON");
		
		URL url = new URL(urlstr);
    		URLConnection conn = url.openConnection();
    		BufferedReader in = new BufferedReader(new InputStreamReader(conn.getInputStream()));
		
		    String inputLine, input = "";
   		while ((inputLine = in.readLine()) != null)
  		 {
      			input = input + inputLine;
    		 }
   		 in.close();
	PrintWriter out = response.getWriter();
        try {
     		 String jsonString = XML.toJSONObject(input).toString(2);
     		 out.println(jsonString);
    	}
    	catch(JSONException je) 
	{
		      out.println(je.toString());
         }
}

public void doPost(HttpServletRequest request, HttpServletResponse response) throws IOException, ServletException { 
		doGet(request, response);
	}

}
