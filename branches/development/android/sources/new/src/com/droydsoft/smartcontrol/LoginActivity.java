package com.droydsoft.smartcontrol;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.content.ContentValues;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.AsyncTask;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import com.droydsoft.smartcontrol.control.HttpRestClient;

public class LoginActivity extends Activity {
	/**
	 * @see android.app.Activity#onCreate(Bundle)
	 */

	private EditText edit_usuario;
	private EditText edit_clave;
	private EditText edit_dir_correo;
	private Button btn_entrar;
	private Button btn_enviar_correo;
	private TextView txt_olvidar_clave;
	private Dialog dialogo_enviar_correo;
	private ProgressBar pb_loguin;

	private SharedPreferences config;
	private SharedPreferences.Editor editor;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.loguin);

		config = getSharedPreferences("config", Context.MODE_PRIVATE);
		editor = config.edit();

		pb_loguin = (ProgressBar) findViewById(R.id.pb_loguin);

		edit_usuario = (EditText) findViewById(R.id.edit_usuario);
		edit_clave = (EditText) findViewById(R.id.edit_clave);
		btn_entrar = (Button) findViewById(R.id.btn_entrar);
		txt_olvidar_clave = (TextView) findViewById(R.id.txt_olvidar_clave);

		btn_entrar.setOnClickListener(click);
		txt_olvidar_clave.setOnClickListener(click);

	}

	private View.OnClickListener click = new View.OnClickListener() {

		public void onClick(View v) {
			switch (v.getId()) {
			case R.id.txt_olvidar_clave:
				dialogo_enviar_correo = new Dialog(LoginActivity.this);
				dialogo_enviar_correo.setTitle(R.string.texto_obtener_clave);
				dialogo_enviar_correo.setContentView(R.layout.enviar_correo);
				dialogo_enviar_correo.show();

				btn_enviar_correo = (Button) dialogo_enviar_correo
						.findViewById(R.id.btn_enviar_correo);

				edit_dir_correo = (EditText) dialogo_enviar_correo
						.findViewById(R.id.edit_dir_correo);

				btn_enviar_correo.setOnClickListener(click);

				break;

			case R.id.btn_entrar:
				//startActivity(new Intent(LoginActivity.this, Principal.class));
				new EjecutarLogin().execute("ok");
				break;

			case R.id.btn_enviar_correo:
				edit_dir_correo = (EditText) dialogo_enviar_correo
						.findViewById(R.id.edit_dir_correo);
				Toast.makeText(
						getBaseContext(),
						getString(R.string.texto_consulte_clave) + " "
								+ edit_dir_correo.getText().toString(),
						Toast.LENGTH_LONG).show();
				break;

			default:
				break;
			}

		}
	};

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

	class EjecutarLogin extends AsyncTask<String, Void, Object> {

		ContentValues resultado1;

		@Override
		protected void onPreExecute() {
			// TODO Auto-generated method stub
			super.onPreExecute();
			edit_clave.setVisibility(View.GONE);
			edit_usuario.setVisibility(View.GONE);
			btn_entrar.setVisibility(View.GONE);
			pb_loguin.setVisibility(View.VISIBLE);

		}

		protected String doInBackground(String... args) {

			// estado_servidor = conectarServicor();
			HttpRestClient ws = new HttpRestClient();

			resultado1 = ws.login(new String[] { "1234567890",
					edit_usuario.getText().toString(),
					edit_clave.getText().toString()				
			});

			return "ya";
		}

		protected void onPostExecute(Object result) {

			if (resultado1.getAsString("status").equals("1")) {
				editor.putString("access_token",
						resultado1.getAsString("access_token"));
				editor.putString("uf",
						resultado1.getAsString("uf"));
				editor.commit();			
				
				startActivity(new Intent(LoginActivity.this, Principal.class));
				finish();
			} else if (resultado1.getAsString("error").equals("-1")) {
				crearDialogoAlerta("Error", "Conectando Servidor").show();
			} else {
				crearDialogoAlerta("Error",
						"Error\n" + resultado1.getAsString("error")).show();
			}

			edit_clave.setVisibility(View.VISIBLE);
			edit_usuario.setVisibility(View.VISIBLE);
			btn_entrar.setVisibility(View.VISIBLE);
			pb_loguin.setVisibility(View.GONE);
			cancel(true);
		}

	}
}
