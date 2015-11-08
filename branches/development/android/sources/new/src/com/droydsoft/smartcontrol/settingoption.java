package com.droydsoft.smartcontrol;

import android.content.Intent;
import android.content.pm.ActivityInfo;
import android.os.Bundle;
import android.preference.CheckBoxPreference;
import android.preference.EditTextPreference;
import android.preference.ListPreference;
import android.preference.Preference;
import android.preference.PreferenceActivity;
import android.preference.PreferenceScreen;

public class settingoption extends PreferenceActivity {
	/**
	 * @see android.app.Activity#onCreate(Bundle)
	 */

	private PreferenceScreen cambiar_clave;
	private CheckBoxPreference notificaciones_activar;
	private EditTextPreference edit_notificaciones_usuario;
	private ListPreference list_notificaciones_tipo_evento;

	@Override
	protected void onStart() {
		// TODO Auto-generated method stub

		super.onStart();
	}

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		// TODO Put your code here
		this.setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_PORTRAIT);
		addPreferencesFromResource(R.xml.setting);

		cambiar_clave = (PreferenceScreen) findPreference("cambiar_clave");

		notificaciones_activar = (CheckBoxPreference) findPreference("notificaciones_activar");
		edit_notificaciones_usuario = (EditTextPreference) findPreference("edit_notificaciones_usuario");
		list_notificaciones_tipo_evento = (ListPreference) findPreference("list_notificaciones_tipo_evento");

		if (notificaciones_activar.isChecked()) {
			edit_notificaciones_usuario.setEnabled(true);
			list_notificaciones_tipo_evento.setEnabled(true);
		} else {
			edit_notificaciones_usuario.setEnabled(false);
			list_notificaciones_tipo_evento.setEnabled(false);
		}

		notificaciones_activar
				.setOnPreferenceChangeListener(new Preference.OnPreferenceChangeListener() {
					@Override
					public boolean onPreferenceChange(Preference preference,
							Object newValue) {
						// TODO Auto-generated method stub

						if (Boolean.parseBoolean(newValue.toString())) {
							edit_notificaciones_usuario.setEnabled(true);
							list_notificaciones_tipo_evento.setEnabled(true);
						} else {
							edit_notificaciones_usuario.setEnabled(false);
							list_notificaciones_tipo_evento.setEnabled(false);
						}
						return true;
					}
				});

		cambiar_clave
				.setOnPreferenceClickListener(new Preference.OnPreferenceClickListener() {

					@Override
					public boolean onPreferenceClick(Preference preference) {
						// TODO Auto-generated method stub

						startActivity(new Intent(settingoption.this,
								CambiarClave.class));

						return true;
					}
				});

	}
	
	


}
