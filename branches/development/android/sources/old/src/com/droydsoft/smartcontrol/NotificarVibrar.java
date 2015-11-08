package com.droydsoft.smartcontrol;

import java.util.Timer;
import java.util.TimerTask;
import java.util.Vector;

import com.droydsoft.smartcontrol.control.HttpRestClient;

import android.app.Notification;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.app.Service;
import android.content.ContentValues;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.AsyncTask;
import android.os.IBinder;
import android.util.Log;

public class NotificarVibrar extends Service {
	/**
	 * @see android.app.Service#onBind(Intent)
	 */
	private static final String TAG = "SmartControl Vibrar";

	private NotificationManager mManager;

	private SharedPreferences config;

	private Timer timer;

	@Override
	public IBinder onBind(Intent intent) {
		return null;
	}
	
	@Override
	public void onDestroy() {
		// TODO Auto-generated method stub
		super.onDestroy();
		timer.cancel();
	}

	@Override
	public void onCreate() {
		super.onCreate();
		Log.d(TAG, "Servicio creado");
		timer = new Timer();
		config = getSharedPreferences("config", Context.MODE_PRIVATE);
	}

	@Override
	public void onStart(final Intent intent, final int startId) {
		super.onStart(intent, startId);
		timer.scheduleAtFixedRate(new TimerTask() {
			@Override
			public void run() {
				// noti();
				if (!(config.getString("fecha_hora", "")).equals("")) {
					Log.i(TAG,
							"Se ejecuta la tarea "
									+ config.getString("fecha_hora", ""));
					
					new EjecutarObtenerEventos("0", "", "", "", "",
							config.getString("fecha_hora", "")).execute("ok");
					
				}

			}
		}, 0, 300000);
	}

	class EjecutarObtenerEventos extends AsyncTask<String, Void, Object> {

		Vector<ContentValues> resultado1;
		String limite;
		String filter_user;
		String filter_evt_type;
		String filter_evt_date_start;		
		String filter_evt_date_end;
		String filter_evt_date_start_not;

		public EjecutarObtenerEventos(String limite, String filter_user,
				String filter_evt_type, String filter_evt_date_start,
				String filter_evt_date_end,String filter_evt_date_start_not) {
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

		protected String doInBackground(String... args) {
			HttpRestClient ws = new HttpRestClient();

			resultado1 = ws.getEventos(config.getString("access_token", ""),
					this.limite, this.filter_user, this.filter_evt_type,
					this.filter_evt_date_start, this.filter_evt_date_end,
					this.filter_evt_date_start_not);
			// resultado1 = ws.getEventos("", "20");
			return "ya";
		}

		protected void onPostExecute(Object result) {
			if (resultado1.get(0).get("status").equals("1")) {
				if ((resultado1.size() - 1) > 1) {
					noti(resultado1.size() - 1 + "");
				}
			}
			cancel(true);
		}
	}

	public void noti(String cantidad) {
		mManager = (NotificationManager) this.getApplicationContext()
				.getSystemService(
						this.getApplicationContext().NOTIFICATION_SERVICE);
		Intent intent1 = new Intent(this.getApplicationContext(), Principal.class);

		Notification notification = new Notification(R.drawable.icon,
				"Usted tiene Notificaciones",
				System.currentTimeMillis());
		intent1.addFlags(Intent.FLAG_ACTIVITY_SINGLE_TOP
				| Intent.FLAG_ACTIVITY_CLEAR_TOP);

		PendingIntent pendingNotificationIntent = PendingIntent.getActivity(
				this.getApplicationContext(), 0, intent1,
				PendingIntent.FLAG_UPDATE_CURRENT);
		notification.flags |= Notification.FLAG_AUTO_CANCEL;
		notification.setLatestEventInfo(this.getApplicationContext(),
				"SmartControl", "Puede ver sus notificaciones pendientes",
				pendingNotificationIntent);

		mManager.notify(0, notification);
	}
}
