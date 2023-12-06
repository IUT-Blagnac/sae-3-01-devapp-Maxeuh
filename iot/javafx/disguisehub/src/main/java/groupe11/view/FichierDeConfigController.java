package groupe11.view;

import java.io.FileWriter;
import java.io.IOException;
import java.util.Map;
import org.yaml.snakeyaml.DumperOptions;
import org.yaml.snakeyaml.Yaml;
import java.util.Arrays;
import java.util.List;
import java.util.List;
import java.util.ArrayList;
import java.util.LinkedHashMap;
import groupe11.control.DisguiseHubApp;
import groupe11.control.DonneesParSalle;
import groupe11.control.FichierDeConfig;
import groupe11.control.VoirLesAlertes;
import groupe11.tools.AlertUtilities;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.CheckBox;
import javafx.scene.control.TextField;
import javafx.scene.control.Alert.AlertType;
import javafx.stage.Stage;

public class FichierDeConfigController {
    private FichierDeConfig fichierDeConfig;

    private Stage configue;

    @FXML
    private TextField hostTextField;

    @FXML
    private TextField portTextField;

    @FXML
    private TextField topicTextField;

    @FXML
    private TextField fichierDataTextField;

    @FXML
    private TextField fichierAlerteTextField;

    @FXML
    private TextField intervalleTextField;

    @FXML
    private TextField co2MinTextField;

    @FXML
    private TextField co2MaxTextField;

    @FXML
    private TextField humidityMinTextField;

    @FXML
    private TextField humidityMaxTextField;

    @FXML
    private TextField pressureMinTextField;

    @FXML
    private TextField pressureMaxTextField;

    @FXML
    private TextField temperatureMinTextField;

    @FXML
    private TextField temperatureMaxTextField;

    @FXML
    private CheckBox co2CheckBox;

    @FXML
    private CheckBox humidityCheckBox;

    @FXML
    private CheckBox pressureCheckBox;

    @FXML
    private CheckBox temperatureCheckBox;

    @FXML
    private CheckBox activityCheckBox;

    @FXML
    private CheckBox tvocCheckBox;

    @FXML
    private CheckBox illuminationCheckBox;

    @FXML
    private CheckBox infraredCheckBox;

    @FXML
    private CheckBox infrared_and_visibleCheckBox;

    @FXML
    private Button validerButton;

    @FXML
    private Button reinitialiserButton;

    private String host;
    private String port;
    private String topic;
    private String fichierData;
    private String fichierAlerte;
    private int intervalle;
    private double co2Min;
    private double co2Max;
    private double humidityMin;
    private double humidityMax;
    private double pressureMin;
    private double pressureMax;
    private double temperatureMin;
    private double temperatureMax;
    private boolean humidity;
    private boolean co2;
    private boolean pressure;
    private boolean temperature;
    private boolean activity;
    private boolean tvoc;
    private boolean illumination;
    private boolean infrared;
    private boolean infrared_and_visible;

    public void initContext(FichierDeConfig fichierDeConfig, Stage configue) {
        this.fichierDeConfig = fichierDeConfig;
        this.configue = configue;

        this.configue.setOnCloseRequest(e -> {
            if (AlertUtilities.confirmYesCancel(this.configue, "confirmation",
                    "Voulez vous vraiment fermer la fenetre", "", AlertType.CONFIRMATION)) {
                this.configue.close();
            } else {
                e.consume();
            }
        });

    }

    public void displayDialog() {
        this.configue.show();
    }

    @FXML
    private void salle() {
        DonneesParSalle controller = new DonneesParSalle(configue);
    }

    @FXML
    private void alerte() {
        VoirLesAlertes controller = new VoirLesAlertes(configue);
    }

    @FXML
    private void accueil() throws Exception {
        DisguiseHubApp cont = new DisguiseHubApp();
        cont.start(configue);
    }

    public int entierDeString(String _val) {
        try {
            int i = Integer.parseInt(_val.trim());
            return i;
        } catch (Exception e) {
            return 0;
        }
    }

    public double doubleDeString(String _val) {
        try {
            double i = Double.parseDouble(_val.trim());
            return i;
        } catch (Exception e) {
            return 0;
        }
    }

    @FXML
    private void validerButton() {
        host = hostTextField.getText().trim();
        port = portTextField.getText().trim();
        topic = topicTextField.getText().trim();
        fichierData = fichierDataTextField.getText().trim();
        fichierAlerte = fichierAlerteTextField.getText().trim();
        intervalle = entierDeString(intervalleTextField.getText());
        co2Min = doubleDeString(co2MinTextField.getText());
        co2Max = doubleDeString(co2MaxTextField.getText().trim());
        humidityMin = doubleDeString(humidityMinTextField.getText().trim());
        humidityMax = doubleDeString(humidityMaxTextField.getText().trim());
        pressureMin = doubleDeString(pressureMinTextField.getText().trim());
        pressureMax = doubleDeString(pressureMaxTextField.getText().trim());
        temperatureMin = doubleDeString(temperatureMinTextField.getText().trim());
        temperatureMax = doubleDeString(temperatureMaxTextField.getText().trim());
        humidity = humidityCheckBox.isSelected();
        co2 = co2CheckBox.isSelected();
        pressure = pressureCheckBox.isSelected();
        temperature = temperatureCheckBox.isSelected();
        activity = activityCheckBox.isSelected();
        tvoc = tvocCheckBox.isSelected();
        illumination = illuminationCheckBox.isSelected();
        infrared = infraredCheckBox.isSelected();
        infrared_and_visible = infrared_and_visibleCheckBox.isSelected();

        String filePath = "iot/python/config.yaml";

        // Créer une structure de données pour représenter la configuration
        Map<String, Object> connectionConfig = new LinkedHashMap<>();
        Map<String, Object> connectionDetails = new LinkedHashMap<>();
        connectionDetails.put("host", host);
        connectionDetails.put("port", port);
        connectionDetails.put("topic", topic);
        connectionConfig.put("connection", connectionDetails);

        // Structure pour la section "ecriture"
        Map<String, Object> config = new LinkedHashMap<>();
        Map<String, Object> ecritureConfig = new LinkedHashMap<>();
        Map<String, Object> fichiers = new LinkedHashMap<>();
        fichiers.put("data", fichierData);
        fichiers.put("alerte", fichierAlerte);
        ecritureConfig.put("fichiers", fichiers);
        ecritureConfig.put("intervale", intervalle);
        config.put("ecriture", ecritureConfig);

        // Créer une structure de données pour représenter la configuration
        Map<String, Object> collecteConfig = new LinkedHashMap<>();
        List<String> collecteList = new ArrayList<>();

        if (temperature)
            collecteList.add("temperature");
        if (activity)
            collecteList.add("activity");
        if (co2)
            collecteList.add("co2");
        if (pressure)
            collecteList.add("pressure");
        if (illumination)
            collecteList.add("illumination");
        if (infrared)
            collecteList.add("infrared");
        if (infrared_and_visible)
            collecteList.add("infrared_and_visible");
        if (humidity)
            collecteList.add("humidity");
        if (tvoc)
            collecteList.add("tvoc");

        collecteConfig.put("collecte", collecteList);

        // Créer une structure de données pour représenter la configuration
        Map<String, Map<String, Map<String, Double>>> alerteConfig = new LinkedHashMap<>();
        Map<String, Map<String, Double>> alerteDetails = new LinkedHashMap<>();

        // Configuration pour chaque type d'alerte
        Map<String, Double> co2 = new LinkedHashMap<>();
        co2.put("min", co2Min);
        co2.put("max", co2Max);
        Map<String, Double> humidity = new LinkedHashMap<>();
        humidity.put("min", humidityMin);
        humidity.put("max", humidityMax);
        Map<String, Double> pressure = new LinkedHashMap<>();
        pressure.put("min", pressureMin);
        pressure.put("max", pressureMax);
        Map<String, Double> temperature = new LinkedHashMap<>();
        temperature.put("min", temperatureMin);
        temperature.put("max", temperatureMax);
        // Ajout des configurations d'alerte
        alerteDetails.put("co2", co2);
        alerteDetails.put("humidity", humidity);
        alerteDetails.put("pressure", pressure);
        alerteDetails.put("temperature", temperature);

        alerteConfig.put("alerte", alerteDetails);

        // Configurer les options de dumping pour conserver l'ordre des propriétés
        DumperOptions options = new DumperOptions();
        options.setAllowReadOnlyProperties(true);
        options.setDefaultFlowStyle(DumperOptions.FlowStyle.BLOCK);
        options.setIndent(2);

        // // Créer un Yaml avec les options personnalisées
        Yaml yaml = new Yaml(options);

        // // Écrire les valeurs dans le fichier YAML
        try (FileWriter writer = new FileWriter(filePath)) {
            yaml.dump(connectionConfig, writer);
            yaml.dump(config, writer);
            yaml.dump(collecteConfig, writer);
            yaml.dump(alerteConfig, writer);
            System.out.println("La configuration a été écrite dans le fichier YAML avec succès.");
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void reinitialiserButton() {
        this.hostTextField.setText("");
        this.portTextField.setText("");
        this.topicTextField.setText("");
        this.fichierDataTextField.setText("");
        this.fichierAlerteTextField.setText("");
        this.intervalleTextField.setText("");
        this.co2MinTextField.setText("");
        this.co2MaxTextField.setText("");
        this.humidityMinTextField.setText("");
        this.humidityMaxTextField.setText("");
        this.pressureMinTextField.setText("");
        this.pressureMaxTextField.setText("");
        this.temperatureMinTextField.setText("");
        this.temperatureMaxTextField.setText("");
        this.co2CheckBox.setSelected(false);
        this.humidityCheckBox.setSelected(false);
        this.pressureCheckBox.setSelected(false);
        this.temperatureCheckBox.setSelected(false);
        this.activityCheckBox.setSelected(false);
        this.tvocCheckBox.setSelected(false);
        this.illuminationCheckBox.setSelected(false);
        this.infraredCheckBox.setSelected(false);
        this.infrared_and_visibleCheckBox.setSelected(false);
    }
}