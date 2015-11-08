package com.droydsoft.smartcontrol;

import java.util.Timer;
import java.util.TimerTask;
import java.util.Vector;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.content.ContentValues;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.drawable.AnimationDrawable;
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.CountDownTimer;
import android.os.Vibrator;
import android.preference.PreferenceManager;
import android.telephony.SmsManager;
import android.util.Log;
import android.view.View;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import com.droydsoft.smartcontrol.control.HttpRestClient;
import com.droydsoft.smartcontrol.modelo.AdaptadorEvento;

public class Principal extends Activity {
	/** Called when the activity is first created. */
	
	//imagnes pantalla principal
	private ImageView img_lupa;
	private ImageView img_anti_panc;
	private ImageView img_emergencia;
	private ImageView img_config;
	private ImageView img_sms;
	private ImageView img_info;
	private ListView listV_eventos;
	
	//ventanas
	private Dialog dialog_filtro;
	private Dialog dialog_emergencia;
	private Dialog dialog_sms;
	private Dialog dialog_info;

	// SMS
	private ImageView img_sms1;
	private ImageView img_sms2;
	private ImageView img_sms3;
	private TextView sms_autor1;
	private TextView sms_autor2;
	private TextView sms_autor3;
	private TextView sms_texto1;
	private TextView sms_texto2;
	private TextView sms_texto3;
	private TextView sms_fecha1;
	private TextView sms_fecha2;
	private TextView sms_fecha3;
	private TextView sms_hora1;
	private TextView sms_hora2;
	private TextView sms_hora3;
	private RelativeLayout layoutR_sms2;
	private RelativeLayout layoutR_sms3;

	// dialog filtro
	private EditText edit_filtrar_usuario;
	private Spinner spinner_filtrar_tipo_evento;
	private EditText edit_filtrar_fecha_inicio;
	private EditText edit_filtrar_fecha_fin;
	private Button btn_filtrar_filtrar;

	// menu emergencia
	private LinearLayout layout_emergencia_emergencia;
	private LinearLayout layout_emergencia_policia;
	private LinearLayout layout_emergencia_bombero;
	private LinearLayout layout_emergencia_ayudamedica;
	private ImageView img_menu_emergencia_emergencia;
	private ImageView img_menu_emergencia_policia;
	private ImageView img_menu_emergencia_bombero;
	private ImageView img_menu_emergencia_ayudamedica;
	private TextView txt_menu_emergencia_numero_emergencia;
	private TextView txt_menu_emergencia_numero_policia;
	private TextView txt_menu_emergencia_numero_bombero;
	private TextView txt_menu_emergencia_numero_ayudamedica;

	private DesconatarTiempo objDesconatarTiempo;

	private AnimationDrawable animacion_anti_panico;

	private boolean estado_boton_anti_panico;

	private ContentValues[] lista_frases;

	protected SharedPreferences prefs;
	private SharedPreferences config;
	private SharedPreferences.Editor editor;
	private Vector<ContentValues> lista_eventos;
	private Vector<ContentValues> lista_sms;

	private AdaptadorEvento adaptador;

	private boolean is_search;
	private int tipo_evento;

	private Timer timer;

	private LocationManager locationManager;
	private LocationListener locationListener;

	private String longitud;
	private String latitud;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.main);

		Intent servicio = new Intent();
		servicio.setAction("notificar.vibrar.Servicio");
		stopService(servicio);

		longitud = "(sin_datos)";
		latitud = "(sin_datos)";

		gps();

		is_search = true;
		tipo_evento = 0;

		config = getSharedPreferences("config", Context.MODE_PRIVATE);
		editor = config.edit();

		lista_eventos = new Vector<ContentValues>();
		lista_sms = new Vector<ContentValues>();
		
		prefs = PreferenceManager.getDefaultSharedPreferences(this);

		estado_boton_anti_panico = false;

		objDesconatarTiempo = new DesconatarTiempo(11000, 1000);

		img_anti_panc = (ImageView) findViewById(R.id.img_anti_panc);
		img_config = (ImageView) findViewById(R.id.img_config);
		img_emergencia = (ImageView) findViewById(R.id.img_emergencia);
		img_info = (ImageView) findViewById(R.id.img_info);
		img_lupa = (ImageView) findViewById(R.id.img_lupa);
		img_sms = (ImageView) findViewById(R.id.img_sms);

		img_anti_panc.setOnClickListener(click);
		img_config.setOnClickListener(click);
		img_emergencia.setOnClickListener(click);
		img_info.setOnClickListener(click);
		img_lupa.setOnClickListener(click);
		img_sms.setOnClickListener(click);

		ejecutar();
		
		new EjecutarObtenerSMS("3", "").execute("ok");

	}

	public void obtenerSMS() {
		config.getString("msg_id_old", "");

	}

	public void clicBtn1(final int position) {		
		if (!(((lista_eventos.get(position).getAsString("url_foto")).toString())
				.equals("null"))) {
			startActivity(new Intent(Intent.ACTION_VIEW,
					Uri.parse((lista_eventos.get(position)
							.getAsString("url_foto")).toString())));
		} else {
			Toast.makeText(getBaseContext(), "No cuenta con foto",
					Toast.LENGTH_LONG).show();
		}

	}

	public void clicBtn2(final int position) {
		if (!(((lista_eventos.get(position).getAsString("url_audio"))
				.toString()).equals("null"))) {
			startActivity(new Intent(Intent.ACTION_VIEW,
					Uri.parse((lista_eventos.get(position)
							.getAsString("url_audio")).toString())));
		} else {
			Toast.makeText(getBaseContext(), "No cuenta con audio",
					Toast.LENGTH_LONG).show();
		}

	}

	public void gps() {
		// Obtenemos una referencia al LocationManager
		locationManager = (LocationManager) getSystemService(Context.LOCATION_SERVICE);

		// Obtenemos la última posición conocida
		// final Location location = locationManager
		// .getLastKnownLocation(LocationManager.GPS_PROVIDER);

		// Mostramos la última posición conocida
		// muestraPosicion(location);

		// Nos registramos para recibir actualizaciones de la posición
		locationListener = new LocationListener() {
			public void onLocationChanged(Location location) {
				muestraPosicion(location);
			}

			public void onProviderDisabled(String provider) {
				// lblEstado.setText("Provider OFF");

				AlertDialog.Builder builder = new AlertDialog.Builder(
						Principal.this);
				builder.setTitle("GPS");
				builder.setMessage("Debe encender el GPS para poder enviar su localización");
				builder.setPositiveButton("Aceptar",
						new DialogInterface.OnClickListener() {
							public void onClick(DialogInterface dialog,
									int which) {
								dialog.cancel();
								Intent intent = new Intent(
										android.provider.Settings.ACTION_LOCATION_SOURCE_SETTINGS);
								startActivity(intent);
							}

						});
				builder.setNegativeButton("Cancelar",
						new DialogInterface.OnClickListener() {
							public void onClick(DialogInterface dialog,
									int which) {
								dialog.cancel();
								locationManager.removeUpdates(locationListener);
							}

						});

				builder.create().show();

			}

			public void onProviderEnabled(String provider) {
				// lblEstado.setText("Provider ON");
			}

			public void onStatusChanged(String provider, int status,
					Bundle extras) {
				Log.i("LocAndroid", "Provider Status: " + status);
				// lblEstado.setText("Provider Status: " + status);
			}
		};

		locationManager.requestLocationUpdates(LocationManager.GPS_PROVIDER, 0,
				0, locationListener);

	}

	public void muestraPosicion(Location loc) {
		if (loc != null) {
			longitud = String.valueOf(loc.getLongitude());
			latitud = String.valueOf(loc.getLatitude());
			
		} else {
			longitud = "(sin_datos)";
			latitud = "(sin_datos)";
			
		}
	}

	public void ejecutar() {
		timer = new Timer();
		timer.scheduleAtFixedRate(new TimerTask() {
			@Override
			public void run() {
				
				Log.i("Ejecuto", "Se ejecuta la tarea");
				new EjecutarObtenerEventos("20", "", "", "", "", "").execute();
			}
		}, 0, 60000);
	}

	@Override
	protected void onDestroy() {
		// TODO Auto-generated method stub
		super.onDestroy();
		Intent servicio = new Intent();
		servicio.setAction("notificar.vibrar.Servicio");
		startService(servicio);
		locationManager.removeUpdates(locationListener);
		timer.cancel();
	}

	class EjecutarObtenerEventos extends AsyncTask<Void, Void, Vector<ContentValues>> {
		//Vector<ContentValues> resultado1;
		String limite;
		String filter_user;
		String filter_evt_type;
		String filter_evt_date_start;
		String filter_evt_date_start_not;
		String filter_evt_date_end;

		public EjecutarObtenerEventos(String limite, String filter_user,
				String filter_evt_type, String filter_evt_date_start,
				String filter_evt_date_start_not, String filter_evt_date_end) {
			super();
			this.limite = limite;
			this.filter_user = filter_user;
			this.filter_evt_type = filter_evt_type;
			this.filter_evt_date_start = filter_evt_date_start;
			this.filter_evt_date_start_not = filter_evt_date_start_not;
			this.filter_evt_date_end = filter_evt_date_end;
		}

		@Override
		protected void onPreExecute() {
			// TODO Auto-generated method stub
			super.onPreExecute();
		}

		protected Vector<ContentValues> doInBackground(Void... args) {
			HttpRestClient ws = new HttpRestClient();

			return ws.getEventos(config.getString("access_token", ""),
					this.limite, this.filter_user, this.filter_evt_type,
					this.filter_evt_date_start, this.filter_evt_date_end,
					this.filter_evt_date_start_not);
			// resultado1 = ws.getEventos("", "20");
			//return resultado1;
		}

		protected void onPostExecute(Vector<ContentValues> resultado) {
			if (resultado.get(0).get("status").equals("1")) {
				lista_eventos.clear();
				if (resultado.size() > 1) {
					editor.putString("fecha_hora", resultado.get(1).get("fecha_hora").toString());
					editor.commit();
					
					for (int i = 1; i < resultado.size(); i++) {
						ContentValues data = resultado.get(i);
						if(data != null){
							try {
								ContentValues valor = new ContentValues();
								valor.put("nombre_user", data.get("nombre").toString());
								valor.put("tipo_evento", Integer.parseInt(data.get("tipo_evento").toString()));
								valor.put("fecha_evento", data.get("fecha").toString());
								valor.put("hora_evento", data.get("hora").toString());
								valor.put("puerta", data.get("puerta") .toString());
								//valor.put("url_audio", data.get("url_audio").toString());
								valor.put("url_foto", data.get("url_img") .toString());
								lista_eventos.add(valor);
							} catch(Exception e) {}
						}						
					}

				} else {
					crearDialogoAlerta("Información",
							"No se encontraron resultados").show();
				}
				adaptador = new AdaptadorEvento(Principal.this, lista_eventos,
						new BtnListView() {

							@Override
							public void onBtnClick(int position) {
								// TODO Auto-generated method stub
								clicBtn1(position);
							}
						}, new BtnListView() {

							@Override
							public void onBtnClick(int position) {
								// TODO Auto-generated method stub
								clicBtn2(position);
							}
						});
				listV_eventos = (ListView) findViewById(R.id.listV_eventos);
				listV_eventos.setAdapter(adaptador);

			} else if (resultado.get(0).get("error").equals("-1")) {
				crearDialogoAlerta("Error", "Conectando Servidor").show();
			} else {
				crearDialogoAlerta("Error",
						resultado.get(0).get("error").toString()).show();
			}

			cancel(true);
		}
	}

	class EjecutarObtenerSMS extends AsyncTask<String, Void, Object> {

		Vector<ContentValues> resultado1;
		String limit;
		String msg_id;

		public EjecutarObtenerSMS(String limit, String msg_id) {
			super();
			this.limit = limit;
			this.msg_id = msg_id;
		}

		@Override
		protected void onPreExecute() {
			// TODO Auto-generated method stub
			super.onPreExecute();
		}

		protected String doInBackground(String... args) {
			HttpRestClient ws = new HttpRestClient();

			resultado1 = ws.getSMS(config.getString("access_token", ""),
					this.limit, this.msg_id);
			
			return "ya";
		}

		protected void onPostExecute(Object result) {
			if (resultado1.get(0).get("status").equals("1")) {

				if (resultado1.size() > 1) {
					int id_old = Integer.parseInt(config.getString(
							"msg_id_old", "0"));
					for (int i = 1; i < resultado1.size(); i++) {
						ContentValues valor = new ContentValues();

						int id_new = Integer.parseInt(resultado1.get(i)
								.get("msg_id").toString());

						if (id_old < id_new) {
							img_sms.setImageResource(R.drawable.sms_lleno);
						}

						valor.put("msg_id", resultado1.get(i).get("msg_id")
								.toString());
						valor.put("msg_author",
								resultado1.get(i).get("msg_author").toString());
						valor.put("msg_content",
								resultado1.get(i).get("msg_content").toString());
						valor.put("msg_fecha", resultado1.get(i).get("fecha")
								.toString());
						valor.put("msg_hora", resultado1.get(i).get("hora")
								.toString());

						lista_sms.add(valor);

					}

				}

			} else if (resultado1.get(0).get("error").equals("-1")) {
				crearDialogoAlerta("Error", "Conectando Servidor").show();
			} else {
				crearDialogoAlerta("Error",
						resultado1.get(0).get("error").toString()).show();
			}

			cancel(true);
		}
	}

	class EjecutarEnviarCorreo extends AsyncTask<String, Void, Object> {

		ContentValues resultado1;
		String correo;
		String mensaje;

		public EjecutarEnviarCorreo(String correo, String mensaje) {
			super();
			this.correo = correo;
			this.mensaje = mensaje;
		}

		@Override
		protected void onPreExecute() {
			// TODO Auto-generated method stub
			super.onPreExecute();
		}

		protected String doInBackground(String... args) {
			HttpRestClient ws = new HttpRestClient();

			resultado1 = ws.enviar_correo(new String[] {
					config.getString("access_token", ""), this.correo,
					this.mensaje });
			
			return "ya";
		}

		protected void onPostExecute(Object result) {
			if (resultado1.get("status").equals("1")) {

			} else if (resultado1.get("error").equals("-1")) {
				crearDialogoAlerta("Error", "Conectando Servidor").show();
			} else {
				crearDialogoAlerta("Error", resultado1.get("error").toString())
						.show();
			}

			cancel(true);
		}
	}

	class EjecutarRegistrarEvento extends AsyncTask<String, Void, Object> {

		ContentValues resultado1;

		@Override
		protected void onPreExecute() {
			// TODO Auto-generated method stub
			super.onPreExecute();
		}

		protected String doInBackground(String... args) {
			HttpRestClient ws = new HttpRestClient();

			resultado1 = ws.registrarEvento(new String[] {
					config.getString("access_token", ""),
					config.getString("uf", ""), "antipanico",
					"Longitud: " + longitud + " Latitud: " + latitud });

			return "ya";
		}

		protected void onPostExecute(Object result) {
			if (resultado1.get("status").equals("1")) {

				Toast.makeText(getBaseContext(), "Emergencia Enviada",
						Toast.LENGTH_LONG).show();

			} else if (resultado1.get("error").equals("-1")) {
				crearDialogoAlerta("Error", "Conectando Servidor").show();
			} else {
				crearDialogoAlerta("Error", resultado1.get("error").toString())
						.show();
			}

			cancel(true);
		}
	}

	public Dialog crearDialogoAlerta(String titulo, String mensaje) {
		AlertDialog.Builder builder = new AlertDialog.Builder(this);

		builder.setTitle(titulo);
		builder.setMessage(mensaje);
		builder.setPositiveButton("Aceptar",
				new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						dialog.cancel();
					}

				});

		return builder.create();
	}	

	class DesconatarTiempo extends CountDownTimer {

		public DesconatarTiempo(long millisInFuture, long countDownInterval) {
			super(millisInFuture, countDownInterval);			

		}

		@Override
		public void onFinish() {
			// TODO Auto-generated method stub
			// tv.setText("changed by the onFinish");
			img_anti_panc.setImageResource(R.drawable.anti_panico);
			estado_boton_anti_panico = false;

			String aviso1_dir = prefs.getString("edit_anti_panico_aviso1_dir",
					"");
			String aviso1_mensaje = prefs.getString(
					"edit_anti_panico_aviso1_mensaje", "")+"\nLongitud: " + longitud + " Latitud: " + latitud;

			if (!(aviso1_dir).equals("") && !(aviso1_mensaje).equals("")) {
				if (aviso1_dir.contains("@")) {
					new EjecutarEnviarCorreo(aviso1_dir, aviso1_mensaje)
							.execute("ok");
				} else {
					SmsManager sms = SmsManager.getDefault();
					sms.sendTextMessage(aviso1_dir, null, aviso1_mensaje, null,
							null);
				}
			} else {
				Toast.makeText(getBaseContext(), "Aviso 1 no está definido",
						Toast.LENGTH_LONG).show();
			}

			String aviso2_dir = prefs.getString("edit_anti_panico_aviso2_dir",
					"");
			String aviso2_mensaje = prefs.getString(
					"edit_anti_panico_aviso2_mensaje", "")+"\nLongitud: " + longitud + " Latitud: " + latitud;
			if (!(aviso2_dir).equals("") && !(aviso2_mensaje).equals("")) {
				if (aviso2_dir.contains("@")) {
					new EjecutarEnviarCorreo(aviso2_dir, aviso2_mensaje)
							.execute("ok");
				} else {
					SmsManager sms = SmsManager.getDefault();
					sms.sendTextMessage(aviso2_dir, null, aviso2_mensaje, null,
							null);
				}
			} else {
				Toast.makeText(getBaseContext(), "Aviso 2 no está definido",
						Toast.LENGTH_LONG).show();
			}

			new EjecutarRegistrarEvento().execute("ok");


		}

		@Override
		public void onTick(long segundos) {
			// TODO Auto-generated method stub

			Vibrator vibrator = (Vibrator) getSystemService(Context.VIBRATOR_SERVICE);
			vibrator.vibrate(50);

			switch ((int) (((segundos + 10) / 1000))) {
			case 2:
				img_anti_panc.setImageResource(R.drawable.ap1);
				break;
			case 3:
				img_anti_panc.setImageResource(R.drawable.ap2);
				break;
			case 4:
				img_anti_panc.setImageResource(R.drawable.ap3);
				break;
			case 5:
				img_anti_panc.setImageResource(R.drawable.ap4);
				break;
			case 6:
				img_anti_panc.setImageResource(R.drawable.ap5);
				break;
			case 7:
				img_anti_panc.setImageResource(R.drawable.ap6);
				break;
			case 8:
				img_anti_panc.setImageResource(R.drawable.ap7);
				break;
			case 9:
				img_anti_panc.setImageResource(R.drawable.ap8);
				break;
			case 10:
				img_anti_panc.setImageResource(R.drawable.ap9);
				break;
			case 11:
				img_anti_panc.setImageResource(R.drawable.ap10);
				break;
			default:

				break;
			}

		}
	}

	public void generarVisualMostrarSMS() {
		layoutR_sms2 = (RelativeLayout) dialog_sms.findViewById(R.id.layoutR_sms2);
		layoutR_sms3 = (RelativeLayout) dialog_sms.findViewById(R.id.layoutR_sms3);
		layoutR_sms2.setVisibility(View.GONE);
		layoutR_sms3.setVisibility(View.GONE);
		img_sms1 = (ImageView) dialog_sms.findViewById(R.id.img_sms1);
		img_sms2 = (ImageView) dialog_sms.findViewById(R.id.img_sms2);
		img_sms3 = (ImageView) dialog_sms.findViewById(R.id.img_sms3);
		sms_autor1 = (TextView) dialog_sms.findViewById(R.id.sms_autor1);
		sms_autor2 = (TextView) dialog_sms.findViewById(R.id.sms_autor2);
		sms_autor3 = (TextView) dialog_sms.findViewById(R.id.sms_autor3);
		sms_texto1 = (TextView) dialog_sms.findViewById(R.id.sms_texto1);
		sms_texto2 = (TextView) dialog_sms.findViewById(R.id.sms_texto2);
		sms_texto3 = (TextView) dialog_sms.findViewById(R.id.sms_texto3);
		sms_fecha1 = (TextView) dialog_sms.findViewById(R.id.sms_fecha1);
		sms_fecha2 = (TextView) dialog_sms.findViewById(R.id.sms_fecha2);
		sms_fecha3 = (TextView) dialog_sms.findViewById(R.id.sms_fecha3);
		sms_hora1 = (TextView) dialog_sms.findViewById(R.id.sms_hora1);
		sms_hora2 = (TextView) dialog_sms.findViewById(R.id.sms_hora2);
		sms_hora3 = (TextView) dialog_sms.findViewById(R.id.sms_hora3);
		String autor = "Autor: ";
		String fecha = "Fecha: ";
		String hora = "Hora: ";
		int id_old = Integer.parseInt(config.getString("msg_id_old", "0"));
		for (int i = 0; i < lista_sms.size(); i++) {
			int id_new = Integer.parseInt(lista_sms.get(i).get("msg_id")
					.toString());
			switch (i) {
			case 0:
				if (id_old < id_new) {
					img_sms1.setVisibility(View.VISIBLE);
				} else {
					img_sms1.setVisibility(View.INVISIBLE);
				}
				sms_autor1.setText(autor
						+ lista_sms.get(i).get("msg_author").toString());
				sms_texto1.setText(lista_sms.get(i).get("msg_content")
						.toString());
				sms_fecha1.setText(fecha
						+ lista_sms.get(i).get("msg_fecha").toString());
				sms_hora1.setText(hora
						+ lista_sms.get(i).get("msg_hora").toString());
				break;

			case 1:		
				layoutR_sms2.setVisibility(View.VISIBLE);
				if (id_old < id_new) {
					img_sms2.setVisibility(View.VISIBLE);
				} else {
					img_sms2.setVisibility(View.INVISIBLE);
				}
				sms_autor2.setText(autor
						+ lista_sms.get(i).get("msg_author").toString());
				sms_texto2.setText(lista_sms.get(i).get("msg_content")
						.toString());
				sms_fecha2.setText(fecha
						+ lista_sms.get(i).get("msg_fecha").toString());
				sms_hora2.setText(hora
						+ lista_sms.get(i).get("msg_hora").toString());
				break;

			case 2:
				layoutR_sms3.setVisibility(View.VISIBLE);
				if (id_old < id_new) {
					img_sms3.setVisibility(View.VISIBLE);
				} else {
					img_sms3.setVisibility(View.INVISIBLE);
				}
				sms_autor3.setText(autor
						+ lista_sms.get(i).get("msg_author").toString());
				sms_texto3.setText(lista_sms.get(i).get("msg_content")
						.toString());
				sms_fecha3.setText(fecha
						+ lista_sms.get(i).get("msg_fecha").toString());
				sms_hora3.setText(hora
						+ lista_sms.get(i).get("msg_hora").toString());
				break;

			default:
				break;
			}
		}

	}

	public void generarVisualMenuEmergencia() {
		txt_menu_emergencia_numero_emergencia = (TextView) dialog_emergencia
				.findViewById(R.id.txt_menu_emergencia_numero_emergencia);
		txt_menu_emergencia_numero_emergencia.setText(prefs.getString(
				"edit_tlf_emergencia", ""));

		txt_menu_emergencia_numero_policia = (TextView) dialog_emergencia
				.findViewById(R.id.txt_menu_emergencia_numero_policia);
		txt_menu_emergencia_numero_policia.setText(prefs.getString(
				"edit_tlf_policia", ""));

		txt_menu_emergencia_numero_bombero = (TextView) dialog_emergencia
				.findViewById(R.id.txt_menu_emergencia_numero_bombero);
		txt_menu_emergencia_numero_bombero.setText(prefs.getString(
				"edit_tlf_bomberos", ""));

		txt_menu_emergencia_numero_ayudamedica = (TextView) dialog_emergencia
				.findViewById(R.id.txt_menu_emergencia_numero_ayudamedica);
		txt_menu_emergencia_numero_ayudamedica.setText(prefs.getString(
				"edit_tlf_ayuda_medica", ""));

		img_menu_emergencia_emergencia = (ImageView) dialog_emergencia
				.findViewById(R.id.img_menu_emergencia_emergencia);
		img_menu_emergencia_emergencia
				.setOnClickListener(new View.OnClickListener() {

					@Override
					public void onClick(View v) {
						// TODO Auto-generated method stub
						if (!prefs.getString("edit_tlf_emergencia", "").equals(
								"")) {
							startActivity(new Intent(Intent.ACTION_DIAL, Uri
									.parse("tel:"
											+ prefs.getString(
													"edit_tlf_emergencia", ""))));
						}
					}
				});

		img_menu_emergencia_policia = (ImageView) dialog_emergencia
				.findViewById(R.id.img_menu_emergencia_policia);
		img_menu_emergencia_policia
				.setOnClickListener(new View.OnClickListener() {

					@Override
					public void onClick(View v) {
						// TODO Auto-generated method stub
						if (!prefs.getString("edit_tlf_policia", "").equals("")) {
							startActivity(new Intent(Intent.ACTION_DIAL, Uri
									.parse("tel:"
											+ prefs.getString(
													"edit_tlf_policia", ""))));
						}
					}
				});

		img_menu_emergencia_bombero = (ImageView) dialog_emergencia
				.findViewById(R.id.img_menu_emergencia_bombero);
		img_menu_emergencia_bombero
				.setOnClickListener(new View.OnClickListener() {

					@Override
					public void onClick(View v) {
						// TODO Auto-generated method stub
						if (!prefs.getString("edit_tlf_bomberos", "")
								.equals("")) {
							startActivity(new Intent(Intent.ACTION_DIAL, Uri
									.parse("tel:"
											+ prefs.getString(
													"edit_tlf_bomberos", ""))));
						}
					}
				});

		img_menu_emergencia_ayudamedica = (ImageView) dialog_emergencia
				.findViewById(R.id.img_menu_emergencia_ayudamedica);
		img_menu_emergencia_ayudamedica
				.setOnClickListener(new View.OnClickListener() {

					@Override
					public void onClick(View v) {
						// TODO Auto-generated method stub
						if (!prefs.getString("edit_tlf_ayuda_medica", "")
								.equals("")) {
							startActivity(new Intent(
									Intent.ACTION_DIAL,
									Uri.parse("tel:"
											+ prefs.getString(
													"edit_tlf_ayuda_medica", ""))));
						}
					}
				});

		layout_emergencia_emergencia = (LinearLayout) dialog_emergencia
				.findViewById(R.id.layout_emergencia_emergencia);
		layout_emergencia_emergencia
				.setOnClickListener(new View.OnClickListener() {

					@Override
					public void onClick(View v) {
						// TODO Auto-generated method stub
						if (!prefs.getString("edit_tlf_emergencia", "").equals(
								"")) {
							startActivity(new Intent(Intent.ACTION_CALL, Uri
									.parse("tel:"
											+ prefs.getString(
													"edit_tlf_emergencia", ""))));
						}
					}
				});

		layout_emergencia_policia = (LinearLayout) dialog_emergencia
				.findViewById(R.id.layout_emergencia_policia);
		layout_emergencia_policia
				.setOnClickListener(new View.OnClickListener() {

					@Override
					public void onClick(View v) {
						// TODO Auto-generated method stub
						if (!prefs.getString("edit_tlf_policia", "").equals("")) {
							startActivity(new Intent(Intent.ACTION_CALL, Uri
									.parse("tel:"
											+ prefs.getString(
													"edit_tlf_policia", ""))));
						}
					}
				});

		layout_emergencia_bombero = (LinearLayout) dialog_emergencia
				.findViewById(R.id.layout_emergencia_bomberos);
		layout_emergencia_bombero
				.setOnClickListener(new View.OnClickListener() {

					@Override
					public void onClick(View v) {
						// TODO Auto-generated method stub
						if (!prefs.getString("edit_tlf_bomberos", "")
								.equals("")) {
							startActivity(new Intent(Intent.ACTION_CALL, Uri
									.parse("tel:"
											+ prefs.getString(
													"edit_tlf_bomberos", ""))));
						}
					}
				});

		layout_emergencia_ayudamedica = (LinearLayout) dialog_emergencia
				.findViewById(R.id.layout_emergencia_ayudamedica);
		layout_emergencia_ayudamedica
				.setOnClickListener(new View.OnClickListener() {

					@Override
					public void onClick(View v) {
						// TODO Auto-generated method stub
						if (!prefs.getString("edit_tlf_ayuda_medica", "")
								.equals("")) {
							startActivity(new Intent(
									Intent.ACTION_CALL,
									Uri.parse("tel:"
											+ prefs.getString(
													"edit_tlf_ayuda_medica", ""))));
						}
					}
				});
	}

	private View.OnClickListener click = new View.OnClickListener() {

		public void onClick(View v) {
			switch (v.getId()) {
			case R.id.img_anti_panc:
				if (!estado_boton_anti_panico) {
					objDesconatarTiempo.start();
					estado_boton_anti_panico = true;
				} else {
					objDesconatarTiempo.cancel();
					img_anti_panc.setImageResource(R.drawable.anti_panico);
					estado_boton_anti_panico = false;
				}
				break;

			case R.id.img_config:
				startActivity(new Intent(Principal.this, settingoption.class));
				break;

			case R.id.img_emergencia:
				dialog_emergencia = new Dialog(Principal.this);
				dialog_emergencia.setTitle(R.string.texto_emergencia);
				dialog_emergencia.setContentView(R.layout.menu_emergencia);

				generarVisualMenuEmergencia();

				dialog_emergencia.show();

				break;

			case R.id.img_info:
				dialog_info = new Dialog(Principal.this);
				dialog_info.setTitle(R.string.texto_politicas_uso);
				dialog_info.setContentView(R.layout.informacion);
				dialog_info.show();
				break;

			case R.id.img_lupa:

				if (is_search) {
					dialog_filtro = new Dialog(Principal.this);
					dialog_filtro.setTitle(R.string.texto_seleccione_filtro);
					dialog_filtro.setContentView(R.layout.filtrar);
					dialog_filtro.show();
					timer.cancel();
					filtro();

				} else {
					is_search = true;
					img_lupa.setImageResource(R.drawable.lupa);
					ejecutar();
				}

				break;

			case R.id.img_sms:			

				if (lista_sms.size() != 0) {
					
					dialog_sms = new Dialog(Principal.this);
					dialog_sms.setTitle(R.string.texto_mostrar_sms);
					dialog_sms.setContentView(R.layout.mostrar_sms);

					img_sms.setImageResource(R.drawable.sms_vacio);

					generarVisualMostrarSMS();

					dialog_sms.show();
					
					editor.putString("msg_id_old",
							lista_sms.get(0).get("msg_id").toString());
					editor.commit();
				} else {
					crearDialogoAlerta("Información",
							"No se encontraron resultados").show();
				}

				break;

			case R.id.btn_filtrar_filtrar:
				new EjecutarObtenerEventos("0", edit_filtrar_usuario.getText()
						.toString(), tipo_evento + "",
						edit_filtrar_fecha_inicio.getText().toString(),
						edit_filtrar_fecha_fin.getText().toString(), "")
						.execute();
				is_search = false;
				img_lupa.setImageResource(R.drawable.cruz_roja);
				dialog_filtro.dismiss();
				break;

			default:
				break;
			}

		}
	};

	public void filtro() {
		edit_filtrar_usuario = (EditText) dialog_filtro
				.findViewById(R.id.edit_filtrar_usuario);
		edit_filtrar_fecha_inicio = (EditText) dialog_filtro
				.findViewById(R.id.edit_filtrar_fecha_inicio);
		edit_filtrar_fecha_fin = (EditText) dialog_filtro
				.findViewById(R.id.edit_filtrar_fecha_fin);
		btn_filtrar_filtrar = (Button) dialog_filtro
				.findViewById(R.id.btn_filtrar_filtrar);
		spinner_filtrar_tipo_evento = (Spinner) dialog_filtro
				.findViewById(R.id.spinner_filtrar_tipo_evento);

		spinner_filtrar_tipo_evento
				.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
					@Override
					public void onItemSelected(AdapterView<?> parent,
							View view, int position, long id) {
						tipo_evento = position;
					}

					@Override
					public void onNothingSelected(AdapterView<?> arg0) {
						// TODO Auto-generated method stub

					}

				});

		btn_filtrar_filtrar.setOnClickListener(click);		
	}
}