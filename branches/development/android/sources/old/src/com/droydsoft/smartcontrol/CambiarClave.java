package com.droydsoft.smartcontrol;

import com.droydsoft.smartcontrol.control.HttpRestClient;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.content.ContentValues;
import android.content.Context;
import android.content.DialogInterface;
import android.content.SharedPreferences;
import android.os.AsyncTask;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;

public class CambiarClave extends Activity {
	/**
	 * @see android.app.Activity#onCreate(Bundle)
	 */

	private EditText edit_clave_vieja_clave;
	private EditText edit_clave_nueva_clave;
	private EditText edit_clave_confirmar_clave;
	private Button btn_clave_enviar;

	private SharedPreferences config;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.cambiar_clave);
		// TODO Put your code here
		edit_clave_vieja_clave = (EditText) findViewById(R.id.edit_clave_vieja_clave);
		edit_clave_nueva_clave = (EditText) findViewById(R.id.edit_clave_nueva_clave);
		edit_clave_confirmar_clave = (EditText) findViewById(R.id.edit_clave_confirmar_clave);

		config = getSharedPreferences("config", Context.MODE_PRIVATE);

		btn_clave_enviar = (Button) findViewById(R.id.btn_clave_enviar);

		btn_clave_enviar.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				if ((edit_clave_nueva_clave.getText().toString())
						.equals(edit_clave_confirmar_clave.getText().toString())) {
					new EjecutarCambiarConatrsena().execute("ok");
				} else {
					crearDialogoAlerta("Error",
							"Contarseña nueva no machea con la confirmación")
							.show();
				}

			}
		});

	}

	class EjecutarCambiarConatrsena extends AsyncTask<String, Void, Object> {

		ContentValues resultado1;

		@Override
		protected void onPreExecute() {
			// TODO Auto-generated method stub
			super.onPreExecute();

		}

		protected String doInBackground(String... args) {

			// estado_servidor = conectarServicor();
			HttpRestClient ws = new HttpRestClient();

			resultado1 = ws.cambiar_contrasena(new String[] {
					config.getString("access_token", ""),
					edit_clave_vieja_clave.getText().toString(),
					edit_clave_nueva_clave.getText().toString() });

			return "ya";
		}

		protected void onPostExecute(Object result) {

			if (resultado1.getAsString("status").equals("1")) {
				crearDialogoAlerta("",
						resultado1.getAsString("result").toString()).show();
			} else if (resultado1.getAsString("error").equals("-1")) {
				crearDialogoAlerta("Error", "Conectando Servidor").show();
			} else {
				crearDialogoAlerta("Error",
						"Error\n" + resultado1.getAsString("error")).show();
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
}
