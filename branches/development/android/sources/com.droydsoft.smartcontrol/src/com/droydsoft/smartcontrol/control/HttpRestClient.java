package com.droydsoft.smartcontrol.control;

import java.io.BufferedReader;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.URI;
import java.net.URLEncoder;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Vector;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.StatusLine;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.BufferedHttpEntity;

import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;
import org.apache.http.params.HttpParams;

import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.content.ContentValues;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Bitmap.CompressFormat;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.net.Uri;
import android.util.Log;
import android.widget.Toast;

public class HttpRestClient {

	private JSONObject jsonObject;

	private String ruta;

	public HttpRestClient() {
		// TODO Auto-generated constructor stub

		// ruta="http://pr2.kiwiio.com/api.php?rquest=get_frases&access_token=token&request_date=";
		// ruta = "http://192.168.0.5";
		// ruta = "http://192.168.100.1";
		ruta = "http://buildingsmartcontrol.com";

	}

	
	public ContentValues login(String[] lista) {
		ContentValues datos = new ContentValues();
		// ruta+=fecha;
		try {
			StringBuilder builder = new StringBuilder();
			HttpClient client = new DefaultHttpClient();

			HttpPost httpPost = new HttpPost(ruta
					+ "/smart_control/api.php/user/login");
			

			List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>();
			
			nameValuePairs
					.add(new BasicNameValuePair("access_token", lista[0]));
			nameValuePairs.add(new BasicNameValuePair("user", lista[1]));
			nameValuePairs.add(new BasicNameValuePair("pass", lista[2]));

			httpPost.setEntity(new UrlEncodedFormEntity(nameValuePairs));

			HttpResponse response = client.execute(httpPost);

			StatusLine statusLine = response.getStatusLine();

			int statusCode = statusLine.getStatusCode();

			HttpEntity entity = response.getEntity();
			InputStream content = entity.getContent();
			BufferedReader reader = new BufferedReader(new InputStreamReader(
					content));

			String line;
			while ((line = reader.readLine()) != null) {
				builder.append(line);
			}

			Log.e("resultado", builder.toString());

			jsonObject = new JSONObject(builder.toString());

			if (statusCode == 200) {

				datos.put("status", jsonObject.getString("status"));
				datos.put("access_token", jsonObject.getString("access_token"));
				datos.put("uf", jsonObject.getJSONObject("user")
						.getString("uf"));
			} else {				
				datos.put("status", "0");
				datos.put("error", jsonObject.getString("error"));

			}

		} catch (ClientProtocolException e) {
			datos.put("status", "0");
			datos.put("error", "-1");
			e.printStackTrace();

		} catch (IOException e) {
			datos.put("status", "0");
			datos.put("error", "-1");
			e.printStackTrace();
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			datos.put("status", "0");
			datos.put("error", "-1");
			e.printStackTrace();
		}

		return datos;
	}

	public ContentValues registrarEvento(String[] lista) {
		ContentValues datos = new ContentValues();
		
		try {
			StringBuilder builder = new StringBuilder();
			HttpClient client = new DefaultHttpClient();

			HttpPost httpPost = new HttpPost(ruta
					+ "/smart_control/api.php/endpoint/operacion/registerAlert");

			List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>();
			
			nameValuePairs
					.add(new BasicNameValuePair("access_token", lista[0]));
			nameValuePairs.add(new BasicNameValuePair("alert_uf", lista[1]));
			nameValuePairs.add(new BasicNameValuePair("alert_type", lista[2]));
			nameValuePairs
					.add(new BasicNameValuePair("alert_coords", lista[3]));

			httpPost.setEntity(new UrlEncodedFormEntity(nameValuePairs));

			HttpResponse response = client.execute(httpPost);

			StatusLine statusLine = response.getStatusLine();

			int statusCode = statusLine.getStatusCode();

			HttpEntity entity = response.getEntity();
			InputStream content = entity.getContent();
			BufferedReader reader = new BufferedReader(new InputStreamReader(
					content));

			String line;
			while ((line = reader.readLine()) != null) {
				builder.append(line);
			}

			Log.e("resultado", builder.toString());

			jsonObject = new JSONObject(builder.toString());

			if (statusCode == 200) {

				datos.put("status", jsonObject.getString("status"));
				
			} else {
				
				datos.put("status", "0");
				datos.put("error", jsonObject.getString("error"));

			}

		} catch (ClientProtocolException e) {
			datos.put("status", "0");
			datos.put("error", "-1");
			e.printStackTrace();

		} catch (IOException e) {
			datos.put("status", "0");
			datos.put("error", "-1");
			e.printStackTrace();
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			datos.put("status", "0");
			datos.put("error", "-1");
			e.printStackTrace();
		}

		return datos;
	}

	public ContentValues enviar_correo(String[] lista) {
		ContentValues datos = new ContentValues();
		
		try {
			StringBuilder builder = new StringBuilder();
			HttpClient client = new DefaultHttpClient();

			HttpPost httpPost = new HttpPost(
					ruta
							+ "/smart_control/api.php/endpoint/operacion/sendNotificationMail");
			

			List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>();
			
			nameValuePairs
					.add(new BasicNameValuePair("access_token", lista[0]));
			nameValuePairs.add(new BasicNameValuePair("email", lista[1]));
			nameValuePairs.add(new BasicNameValuePair("message", lista[2]));

			httpPost.setEntity(new UrlEncodedFormEntity(nameValuePairs));

			HttpResponse response = client.execute(httpPost);

			StatusLine statusLine = response.getStatusLine();

			int statusCode = statusLine.getStatusCode();

			HttpEntity entity = response.getEntity();
			InputStream content = entity.getContent();
			BufferedReader reader = new BufferedReader(new InputStreamReader(
					content));

			String line;
			while ((line = reader.readLine()) != null) {
				builder.append(line);
			}

			Log.e("resultado", builder.toString());

			jsonObject = new JSONObject(builder.toString());

			if (statusCode == 200) {

				datos.put("status", jsonObject.getString("status"));
				datos.put("result", jsonObject.getString("result"));

			} else {
				
				datos.put("status", "0");
				datos.put("error", jsonObject.getString("error"));

			}

		} catch (ClientProtocolException e) {
			datos.put("status", "0");
			datos.put("error", "-1");
			e.printStackTrace();

		} catch (IOException e) {
			datos.put("status", "0");
			datos.put("error", "-1");
			e.printStackTrace();
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			datos.put("status", "0");
			datos.put("error", "-1");
			e.printStackTrace();
		}

		return datos;
	}

	public ContentValues cambiar_contrasena(String[] lista) {
		ContentValues datos = new ContentValues();
		
		try {
			StringBuilder builder = new StringBuilder();
			HttpClient client = new DefaultHttpClient();

			HttpPost httpPost = new HttpPost(ruta
					+ "/smart_control/api.php/user/");
			

			List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>();
			
			nameValuePairs
					.add(new BasicNameValuePair("access_token", lista[0]));
			nameValuePairs.add(new BasicNameValuePair("old_passwd", lista[1]));
			nameValuePairs.add(new BasicNameValuePair("new_passwd", lista[2]));

			httpPost.setEntity(new UrlEncodedFormEntity(nameValuePairs));

			HttpResponse response = client.execute(httpPost);

			StatusLine statusLine = response.getStatusLine();

			int statusCode = statusLine.getStatusCode();

			HttpEntity entity = response.getEntity();
			InputStream content = entity.getContent();
			BufferedReader reader = new BufferedReader(new InputStreamReader(
					content));

			String line;
			while ((line = reader.readLine()) != null) {
				builder.append(line);
			}

			Log.e("resultado", builder.toString());

			jsonObject = new JSONObject(builder.toString());

			if (statusCode == 200) {

				datos.put("status", jsonObject.getString("status"));
				datos.put("result", jsonObject.getString("result"));
			} else {
				
				datos.put("status", "0");
				datos.put("error", jsonObject.getString("error"));

			}

		} catch (ClientProtocolException e) {
			datos.put("status", "0");
			datos.put("error", "-1");
			e.printStackTrace();

		} catch (IOException e) {
			datos.put("status", "0");
			datos.put("error", "-1");
			e.printStackTrace();
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			datos.put("status", "0");
			datos.put("error", "-1");
			e.printStackTrace();
		}

		return datos;
	}

	
	public Vector<ContentValues> getSMS(String token, String limit,
			String msg_id) {
		Vector<ContentValues> datos = new Vector<ContentValues>();
		
		ContentValues temp = new ContentValues();
		try {
			StringBuilder builder = new StringBuilder();
			HttpClient client = new DefaultHttpClient();

			HttpGet httpGet = new HttpGet(
					ruta
							+ "/smart_control/api.php/endpoint/operacion/getMessagesData?"
							+ "access_token=" + Uri.encode(token) + "&limit="
			+ Uri.encode(limit) + "&msg_id="
			+ Uri.encode(msg_id)
							
					);
			
			HttpResponse response = client.execute(httpGet);

			StatusLine statusLine = response.getStatusLine();

			int statusCode = statusLine.getStatusCode();

			HttpEntity entity = response.getEntity();
			InputStream content = entity.getContent();
			BufferedReader reader = new BufferedReader(new InputStreamReader(
					content));

			String line;
			while ((line = reader.readLine()) != null) {
				builder.append(line);
			}

			Log.e("resultado", builder.toString());

			jsonObject = new JSONObject(builder.toString());

			if (statusCode == 200) {

				temp.put("status", jsonObject.getString("status"));
				datos.add(temp);

				for (int i = 0; i < jsonObject.getJSONArray("result").length(); i++) {
					temp = new ContentValues();
					temp.put("msg_id", jsonObject.getJSONArray("result")
							.getJSONObject(i).getString("msg_id"));
					temp.put("msg_author", jsonObject.getJSONArray("result")
							.getJSONObject(i).getString("msg_author"));
					temp.put("msg_title", jsonObject.getJSONArray("result")
							.getJSONObject(i).getString("msg_title"));
					temp.put("msg_content", jsonObject.getJSONArray("result")
							.getJSONObject(i).getString("msg_content"));
					temp.put("fecha", jsonObject.getJSONArray("result")
							.getJSONObject(i).getString("msg_date_date")); //
					temp.put("hora", jsonObject.getJSONArray("result") //
							.getJSONObject(i).getString("msg_date_hour")); //					
					datos.add(temp);
				}

			} else {
				
				temp.put("status", "0");
				temp.put("error", jsonObject.getString("error"));
				datos.add(temp);

			}

		} catch (ClientProtocolException e) {
			temp.put("status", "0");
			temp.put("error", "-1");
			datos.add(temp);
			e.printStackTrace();

		} catch (IOException e) {
			temp.put("status", "0");
			temp.put("error", "-1");
			datos.add(temp);
			e.printStackTrace();
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			temp.put("status", "0");
			temp.put("error", "-1");
			datos.add(temp);
			e.printStackTrace();
		}

		return datos;
	}

	public Vector<ContentValues> getEventos(String token, String limit,
			String filter_user, String filter_evt_type,
			String filter_evt_date_start, String filter_evt_date_end,
			String filter_evt_date_start_not) {
		Vector<ContentValues> datos = new Vector<ContentValues>();
		
		ContentValues temp = new ContentValues();
		

		try {
			StringBuilder builder = new StringBuilder();
			HttpClient client = new DefaultHttpClient();

			HttpGet httpGet = new HttpGet(
					ruta
							+ "/smart_control/api.php/endpoint/operacion/getEventosData?"
							+ "access_token=" + Uri.encode(token) + "&limit="
							+ Uri.encode(limit) + "&filter_user="
							+ Uri.encode(filter_user) + "&filter_evt_type="
							+ Uri.encode(filter_evt_type)
							+ "&filter_evt_date_start="
							+ Uri.encode(filter_evt_date_start)
							+ "&filter_evt_date_end="
							+ Uri.encode(filter_evt_date_end)
							+ "&filter_evt_date_start_not="
							+ Uri.encode(filter_evt_date_start_not));
			

			HttpResponse response = client.execute(httpGet);

			StatusLine statusLine = response.getStatusLine();

			int statusCode = statusLine.getStatusCode();

			HttpEntity entity = response.getEntity();
			InputStream content = entity.getContent();
			BufferedReader reader = new BufferedReader(new InputStreamReader(
					content));

			String line;
			while ((line = reader.readLine()) != null) {
				builder.append(line);
			}

			Log.e("resultado", builder.toString());

			jsonObject = new JSONObject(builder.toString());

			if (statusCode == 200) {
				temp.put("status", jsonObject.getString("status"));
				datos.add(temp);
				for (int i = 0; i < jsonObject.getJSONArray("result").length(); i++) {
					temp = new ContentValues();
					temp.put("tipo_evento", jsonObject.getJSONArray("result")
							.getJSONObject(i).getString("evt_type"));
					temp.put("fecha_hora", jsonObject.getJSONArray("result")
							.getJSONObject(i).getString("evt_date"));
					temp.put("nombre", jsonObject.getJSONArray("result")
							.getJSONObject(i).getString("usr_name"));
					temp.put("fecha", jsonObject.getJSONArray("result")
							.getJSONObject(i).getString("evt_date_date"));
					temp.put("hora", jsonObject.getJSONArray("result")
							.getJSONObject(i).getString("evt_date_hour"));
					temp.put("puerta", jsonObject.getJSONArray("result")
							.getJSONObject(i).getString("evt_door"));
					
					temp.put("url_img", jsonObject.getJSONArray("result")
							.getJSONObject(i).getString("evt_img1"));
					temp.put("url_audio", jsonObject.getJSONArray("result")
							.getJSONObject(i).getString("evt_img2"));
					datos.add(temp);
				}

			} else {
				
				temp.put("status", "0");
				temp.put("error", jsonObject.getString("error"));
				datos.add(temp);

			}

		} catch (ClientProtocolException e) {
			temp.put("status", "0");
			temp.put("error", "-1");
			datos.add(temp);
			e.printStackTrace();

		} catch (IOException e) {
			temp.put("status", "0");
			temp.put("error", "-1");
			datos.add(temp);
			e.printStackTrace();
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			temp.put("status", "0");
			temp.put("error", "-1");
			datos.add(temp);
			e.printStackTrace();
		}

		return datos;
	}
}
